<?php

namespace App\Http\Controllers;

use App\Enums\TravelRegistrationStatus;
use App\Enums\UserRole;
use App\Models\CabangTravel;
use App\Models\TravelCompany;
use App\Models\User;
use App\Notifications\V2\CabangRegistrationSubmittedNotification;
use App\Notifications\V2\TravelRegistrationSubmittedNotification;
use App\Services\NotificationService;
use App\Helpers\ValidationHelper;
use App\Support\NtbKabupatenMap;
use App\Support\RegistrationFileStash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TravelRegistrationController extends Controller
{
    public function create(RegistrationFileStash $berkas)
    {
        return view('travel-registration.create', [
            'kabupatens' => NtbKabupatenMap::names(),
            'berkasTersimpan' => $berkas->names(),
        ]);
    }

    public function store(Request $request, RegistrationFileStash $berkas)
    {
        $this->releaseRejectedRegistrationCredentials($request);

        $fileMaxKb = ValidationHelper::fileMaxKb(1.5);

        // Berkas yang sudah benar disimpan dulu, supaya kesalahan di isian lain
        // tidak memaksa pendaftar mengunggah ulang semuanya.
        $berkas->capture($request, ['dokumen_sk', 'dokumen_akreditasi'], $fileMaxKb);

        $rules = array_merge(ValidationHelper::travelCompanyDataRules(), [
            'pic_nama' => 'required|string|max:255',
            'pic_email' => 'required|email|max:255|unique:users,email',
            'pic_nomor_hp' => ValidationHelper::nomorHpRules(uniqueInUsers: true),
            'password' => 'required|string|min:8|confirmed',
            // Pusat hanya wajib SK izin. Sertifikat akreditasi opsional, nilainya
            // sendiri sudah diisi sebagai data pada langkah akreditasi.
            'dokumen_sk' => ($berkas->has('dokumen_sk') ? 'nullable' : 'required')."|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
            'dokumen_akreditasi' => "nullable|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
        ]);

        $validated = ValidationHelper::validate($request, $rules, array_merge(
            ValidationHelper::fileMaxMb('dokumen_sk', 1.5),
            ValidationHelper::fileMaxMb('dokumen_akreditasi', 1.5),
            [
                'kab_kota.in' => 'Pilih kabupaten/kota yang ada di NTB.',
                'dokumen_sk.mimes' => self::PESAN_FORMAT_BERKAS,
                'dokumen_akreditasi.mimes' => self::PESAN_FORMAT_BERKAS,
            ]
        ));

        $travel = DB::transaction(function () use ($berkas, $validated) {
            $travelData = collect($validated)->only([
                'Penyelenggara',
                'Status',
                'Pusat',
                'Tanggal',
                'nilai_akreditasi',
                'tanggal_akreditasi',
                'lembaga_akreditasi',
                'Pimpinan',
                'Telepon',
                'alamat_kantor_lama',
                'alamat_kantor_baru',
                'kab_kota',
                'license_expiry',
            ])->all();

            $travelData['registration_status'] = TravelRegistrationStatus::Pending;
            $travelData['dokumen_sk'] = $berkas->planMove('dokumen_sk', 'registrasi-travel/sk');

            if ($berkas->has('dokumen_akreditasi')) {
                $travelData['dokumen_akreditasi'] = $berkas->planMove('dokumen_akreditasi', 'registrasi-travel/akreditasi');
            }

            $travel = TravelCompany::create($travelData);
            $travel->setDefaultCapabilities();
            $travel->description = $travel->getTravelTypeDescription();
            $travel->save();

            User::create([
                'nama' => $validated['pic_nama'],
                'email' => $validated['pic_email'],
                'nomor_hp' => $validated['pic_nomor_hp'],
                'password' => Hash::make($validated['password']),
                'role' => UserRole::User->value,
                'travel_id' => $travel->id,
                'kabupaten' => $travel->kab_kota,
                'country' => 'Indonesia',
                // Password dibuat sendiri oleh pendaftar, bukan password default,
                // jadi jangan dipaksa ganti saat login pertama.
                'is_password_changed' => true,
            ]);

            return $travel;
        });

        // Berkas baru dipindahkan setelah transaksi berhasil, supaya kegagalan
        // menyimpan tidak ikut menghapus simpanan pendaftar.
        $berkas->commitPendingMoves();
        $berkas->clear();

        app(NotificationService::class)->notifyReviewers(
            $travel,
            new TravelRegistrationSubmittedNotification($travel)
        );

        return redirect()
            ->route('travel.registration.success')
            ->with('success', 'Pendaftaran berhasil dikirim. Tim Kanwil akan memverifikasi data Anda.');
    }

    public function createCabang(RegistrationFileStash $berkas)
    {
        return view('travel-registration.create-cabang', [
            'kabupatens' => NtbKabupatenMap::names(),
            'berkasTersimpan' => $berkas->names(),
            'travels' => TravelCompany::approved()
                ->select('id', 'Penyelenggara', 'Pusat', 'Pimpinan', 'kab_kota')
                ->orderBy('Penyelenggara')
                ->get(),
        ]);
    }

    public function storeCabang(Request $request, RegistrationFileStash $berkas)
    {
        // Email dan HP disamakan formatnya dulu, supaya "Nama@Mail.com" atau
        // "+62 812..." tidak lolos cek unik lalu gagal saat dipakai login.
        $request->merge(array_filter([
            'pic_email' => $request->filled('pic_email') ? strtolower($request->input('pic_email')) : null,
            'pic_nomor_hp' => $request->filled('pic_nomor_hp') ? User::normalizeNomorHp($request->input('pic_nomor_hp')) : null,
        ]));

        $this->releaseRejectedRegistrationCredentials($request);

        $fileMaxKb = ValidationHelper::fileMaxKb(1.5);
        $dokumenRules = [];
        $dokumenMessages = [];

        // Berkas yang sudah benar disimpan dulu, supaya kesalahan di isian lain
        // tidak memaksa pendaftar mengunggah ulang semuanya.
        $berkas->capture($request, array_merge(
            array_column(CabangTravel::DOKUMEN_PENDAFTARAN, 'column'),
            ['dokumen_sk_pusat'],
        ), $fileMaxKb);

        foreach (CabangTravel::DOKUMEN_PENDAFTARAN as $type => $meta) {
            $dokumenRules[$meta['column']] = ($berkas->has($meta['column']) ? 'nullable' : 'required')
                ."|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}";
            $dokumenMessages = array_merge($dokumenMessages, ValidationHelper::fileMaxMb($meta['column'], 1.5), [
                "{$meta['column']}.mimes" => self::PESAN_FORMAT_BERKAS,
            ]);
        }

        $validated = ValidationHelper::validate($request, array_merge([
            // Pusat yang terdata dipilih dari daftar travel yang izinnya sudah
            // disetujui, dari sinilah nomor SK dan identitas pusat dibaca. Pusat
            // di luar NTB belum tentu terdata, jadi identitas dan SK-nya diisi
            // manual lalu diperiksa Kabupaten/Kota dan Kanwil seperti berkas lain.
            'pusat_terdaftar' => 'required|boolean',
            'travel_id' => ['exclude_unless:pusat_terdaftar,1', 'required', 'integer', Rule::exists('travels', 'id')->where('registration_status', TravelRegistrationStatus::Approved->value)],
            'Penyelenggara' => 'exclude_unless:pusat_terdaftar,0|required|string|max:255',
            'pusat' => ['exclude_unless:pusat_terdaftar,0', 'required', 'string', 'max:255', $this->pusatBelumTerdataRule()],
            'pimpinan_pusat' => 'exclude_unless:pusat_terdaftar,0|required|string|max:255',
            'alamat_pusat' => 'exclude_unless:pusat_terdaftar,0|'.ValidationHelper::textRule(),
            'dokumen_sk_pusat' => 'exclude_unless:pusat_terdaftar,0|'.($berkas->has('dokumen_sk_pusat') ? 'nullable' : 'required')
                ."|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
            // Satu pusat hanya boleh punya satu pendaftaran cabang aktif per
            // wilayah. Tanpa ini kantor yang sama bisa didaftarkan berkali kali
            // asal memakai email PIC berbeda, dan Kabko melihat antrean ganda.
            'kabupaten' => [
                'required',
                'string',
                Rule::in(NtbKabupatenMap::names()),
                Rule::unique('travel_cabang', 'kabupaten')->where(fn ($query) => $query
                    ->when(
                        $request->boolean('pusat_terdaftar'),
                        fn ($q) => $q->where('travel_id', $request->input('travel_id')),
                        // Pusat manual diketik tangan, jadi "PT. Nur Islami" dan
                        // "pt nur islami" dianggap sama, begitu juga nomor SK-nya.
                        fn ($q) => $q->whereNull('travel_id')->where(fn ($same) => $same
                            ->whereRaw(self::NAMA_RINGKAS_SQL.' = ?', [self::namaRingkas((string) $request->input('Penyelenggara'))])
                            ->orWhereRaw('LOWER(TRIM(pusat)) = ?', [strtolower(trim((string) $request->input('pusat')))])),
                    )
                    ->whereIn('registration_status', [
                        TravelRegistrationStatus::Pending->value,
                        TravelRegistrationStatus::MenungguKanwil->value,
                        TravelRegistrationStatus::Approved->value,
                    ])),
            ],
            'SK_BA' => 'required|string|max:255',
            'tanggal' => 'required|date|before_or_equal:today',
            'pimpinan_cabang' => 'required|string|max:255',
            'alamat_cabang' => ValidationHelper::textRule(),
            'telepon' => ValidationHelper::teleponRules(),
            'pic_nama' => 'required|string|max:255',
            'pic_email' => 'required|email|max:255|unique:users,email',
            'pic_nomor_hp' => ValidationHelper::nomorHpRules(uniqueInUsers: true),
            'password' => 'required|string|min:8|confirmed',
        ], $dokumenRules), array_merge($dokumenMessages, ValidationHelper::fileMaxMb('dokumen_sk_pusat', 1.5), [
            'pusat_terdaftar.required' => 'Pilih apakah travel pusat sudah terdaftar di sistem.',
            'travel_id.required' => 'Pilih travel pusat yang menaungi cabang ini.',
            'Penyelenggara.required' => 'Isi nama travel pusat.',
            'pusat.required' => 'Isi nomor SK izin PPIU pusat.',
            'pimpinan_pusat.required' => 'Isi nama pimpinan pusat.',
            'dokumen_sk_pusat.required' => 'Unggah SK izin PPIU pusat.',
            'dokumen_sk_pusat.mimes' => self::PESAN_FORMAT_BERKAS,
            'travel_id.exists' => 'Travel pusat tidak ditemukan atau izinnya belum disetujui.',
            'kabupaten.in' => 'Pilih kabupaten/kota yang ada di NTB.',
            'kabupaten.unique' => 'Cabang travel ini di kabupaten/kota tersebut sudah pernah didaftarkan. Hubungi Kanwil bila statusnya belum juga diproses.',
            'SK_BA.required' => 'Isi nomor SK / berita acara pembukaan cabang.',
            'tanggal.before_or_equal' => 'Tanggal SK / BA tidak boleh melewati hari ini.',
        ]));

        $cabang = DB::transaction(function () use ($berkas, $validated) {
            $data = collect($validated)->only([
                'travel_id',
                'Penyelenggara',
                'pusat',
                'pimpinan_pusat',
                'alamat_pusat',
                'kabupaten',
                'SK_BA',
                'tanggal',
                'pimpinan_cabang',
                'alamat_cabang',
                'telepon',
            ])->all();

            if (isset($validated['travel_id'])) {
                // Identitas pusat tidak diketik ulang, ikut data pusat yang dipilih.
                $pusat = TravelCompany::findOrFail($validated['travel_id']);
                $data['Penyelenggara'] = $pusat->Penyelenggara;
                $data['pusat'] = $pusat->Pusat;
                $data['pimpinan_pusat'] = $pusat->Pimpinan;
                $data['alamat_pusat'] = $pusat->alamat_kantor_baru ?: $pusat->alamat_kantor_lama;
            } else {
                $data['dokumen_sk_pusat'] = $berkas->planMove('dokumen_sk_pusat', 'registrasi-cabang/sk_pusat');
            }
            $data['registration_status'] = TravelRegistrationStatus::Pending;

            foreach (CabangTravel::DOKUMEN_PENDAFTARAN as $type => $meta) {
                $data[$meta['column']] = $berkas->planMove($meta['column'], "registrasi-cabang/{$type}");
            }

            $cabang = CabangTravel::create($data);

            User::create([
                'nama' => $validated['pic_nama'],
                'email' => $validated['pic_email'],
                'nomor_hp' => $validated['pic_nomor_hp'],
                'password' => Hash::make($validated['password']),
                'role' => UserRole::User->value,
                'cabang_id' => $cabang->id_cabang,
                'kabupaten' => $cabang->kabupaten,
                'country' => 'Indonesia',
                // Password dibuat sendiri oleh pendaftar, bukan password default,
                // jadi jangan dipaksa ganti saat login pertama.
                'is_password_changed' => true,
            ]);

            return $cabang;
        });

        $berkas->commitPendingMoves();
        $berkas->clear();

        app(NotificationService::class)->notifyReviewersInKabupaten(
            $cabang->kabupaten,
            new CabangRegistrationSubmittedNotification($cabang)
        );

        return redirect()
            ->route('travel.registration.success')
            ->with('jenis_pendaftaran', 'cabang')
            ->with('success', 'Pendaftaran cabang berhasil dikirim. Kantor Kemenhaj Kabupaten/Kota akan melakukan peninjauan terlebih dahulu.');
    }

    private const PESAN_FORMAT_BERKAS = ':attribute harus berformat PDF, JPG, atau PNG. Foto iPhone (HEIC) ubah dulu ke JPG.';

    /** Nama PT tanpa titik, koma, spasi, dan huruf besar, untuk mendeteksi ketikan ganda. */
    private const NAMA_RINGKAS_SQL = "REPLACE(REPLACE(REPLACE(LOWER(Penyelenggara), '.', ''), ',', ''), ' ', '')";

    private static function namaRingkas(string $nama): string
    {
        return str_replace(['.', ',', ' '], '', strtolower($nama));
    }

    /**
     * Jalur manual hanya untuk pusat yang belum ada di sistem. Kalau nomor SK
     * yang diketik ternyata milik pusat terdata, cabang itu harus menempel ke
     * pusatnya, bukan jadi entri lepas yang tidak terhubung.
     */
    private function pusatBelumTerdataRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $pusat = TravelCompany::query()
                ->whereRaw('LOWER(TRIM(Pusat)) = ?', [strtolower(trim((string) $value))])
                ->where('registration_status', '!=', TravelRegistrationStatus::Rejected->value)
                ->first(['registration_status']);

            if ($pusat === null) {
                return;
            }

            $fail($pusat->registration_status === TravelRegistrationStatus::Approved
                ? 'Pusat dengan nomor SK ini sudah terdaftar. Pilih "Sudah, pilih dari daftar" lalu cari nama pusatnya.'
                : 'Pusat dengan nomor SK ini masih diproses Kanwil. Daftarkan cabang setelah pusatnya disetujui.');
        };
    }

    private function releaseRejectedRegistrationCredentials(Request $request): void
    {
        foreach (['pic_email' => 'email', 'pic_nomor_hp' => 'nomor_hp'] as $field => $column) {
            if (! $request->filled($field)) {
                continue;
            }

            $rejected = fn ($query) => $query->where('registration_status', TravelRegistrationStatus::Rejected);

            User::query()
                ->where($column, $request->input($field))
                ->where(fn ($query) => $query
                    ->whereHas('travel', $rejected)
                    ->orWhereHas('cabang', $rejected))
                ->each(fn (User $user) => $user->delete());
        }
    }

    public function success()
    {
        return view('travel-registration.success', [
            'jenis' => session('jenis_pendaftaran', 'pusat'),
        ]);
    }
}
