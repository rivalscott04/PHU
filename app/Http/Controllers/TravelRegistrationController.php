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
                        TravelRegistrationStatus::PerluPerbaikan->value,
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

    public function editRevision(RegistrationFileStash $berkas)
    {
        $user = auth()->user();
        $registration = $user?->operatingRegistration();

        abort_unless($registration?->isNeedsRevision(), 404);

        $berkasTersimpan = $berkas->names();

        if ($registration instanceof TravelCompany) {
            foreach (['dokumen_sk' => 'dokumen_sk', 'dokumen_akreditasi' => 'dokumen_akreditasi'] as $field => $column) {
                if (empty($berkasTersimpan[$field]) && $registration->{$column}) {
                    $berkasTersimpan[$field] = basename($registration->{$column});
                }
            }

            return view('travel-registration.create', [
                'isRevision' => true,
                'travel' => $registration,
                'pic' => $user,
                'kabupatens' => NtbKabupatenMap::names(),
                'berkasTersimpan' => $berkasTersimpan,
                'revisionNotes' => $registration->registration_notes,
            ]);
        }

        foreach (CabangTravel::DOKUMEN_PENDAFTARAN as $meta) {
            $column = $meta['column'];
            if (empty($berkasTersimpan[$column]) && $registration->{$column}) {
                $berkasTersimpan[$column] = basename($registration->{$column});
            }
        }
        if (empty($berkasTersimpan['dokumen_sk_pusat']) && $registration->dokumen_sk_pusat) {
            $berkasTersimpan['dokumen_sk_pusat'] = basename($registration->dokumen_sk_pusat);
        }

        return view('travel-registration.create-cabang', [
            'isRevision' => true,
            'cabang' => $registration,
            'pic' => $user,
            'kabupatens' => NtbKabupatenMap::names(),
            'berkasTersimpan' => $berkasTersimpan,
            'revisionNotes' => $registration->registration_notes,
            'travels' => TravelCompany::approved()
                ->select('id', 'Penyelenggara', 'Pusat', 'Pimpinan', 'kab_kota')
                ->orderBy('Penyelenggara')
                ->get(),
        ]);
    }

    public function updateRevision(Request $request, RegistrationFileStash $berkas)
    {
        $user = auth()->user();
        $registration = $user?->operatingRegistration();

        abort_unless($registration?->isNeedsRevision(), 404);

        if ($registration instanceof TravelCompany) {
            return $this->updatePusatRevision($request, $berkas, $registration, $user);
        }

        return $this->updateCabangRevision($request, $berkas, $registration, $user);
    }

    private function updatePusatRevision(Request $request, RegistrationFileStash $berkas, TravelCompany $travel, User $user)
    {
        $request->merge(array_filter([
            'pic_email' => $request->filled('pic_email') ? strtolower($request->input('pic_email')) : null,
            'pic_nomor_hp' => $request->filled('pic_nomor_hp') ? User::normalizeNomorHp($request->input('pic_nomor_hp')) : null,
        ]));

        $fileMaxKb = ValidationHelper::fileMaxKb(1.5);
        $berkas->capture($request, ['dokumen_sk', 'dokumen_akreditasi'], $fileMaxKb);

        $hasSk = $berkas->has('dokumen_sk') || filled($travel->dokumen_sk);

        $rules = array_merge(ValidationHelper::travelCompanyDataRules($travel->id), [
            'pic_nama' => 'required|string|max:255',
            'pic_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'pic_nomor_hp' => ValidationHelper::nomorHpRules(uniqueInUsers: true, ignoreUserId: $user->id),
            'password' => 'nullable|string|min:8|confirmed',
            'dokumen_sk' => ($hasSk ? 'nullable' : 'required')."|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
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

        DB::transaction(function () use ($berkas, $validated, $travel, $user) {
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

            if ($berkas->has('dokumen_sk')) {
                if ($travel->dokumen_sk) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($travel->dokumen_sk);
                }
                $travelData['dokumen_sk'] = $berkas->planMove('dokumen_sk', 'registrasi-travel/sk');
            }

            if ($berkas->has('dokumen_akreditasi')) {
                if ($travel->dokumen_akreditasi) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($travel->dokumen_akreditasi);
                }
                $travelData['dokumen_akreditasi'] = $berkas->planMove('dokumen_akreditasi', 'registrasi-travel/akreditasi');
            }

            $travelData['registration_status'] = TravelRegistrationStatus::Pending;
            $travelData['registration_notes'] = null;
            $travelData['revision_return_status'] = null;

            $travel->update($travelData);
            $travel->setDefaultCapabilities();
            $travel->description = $travel->getTravelTypeDescription();
            $travel->save();

            $picData = [
                'nama' => $validated['pic_nama'],
                'email' => $validated['pic_email'],
                'nomor_hp' => $validated['pic_nomor_hp'],
                'kabupaten' => $travel->kab_kota,
            ];
            if (! empty($validated['password'])) {
                $picData['password'] = Hash::make($validated['password']);
            }
            $user->update($picData);
        });

        $berkas->commitPendingMoves();
        $berkas->clear();

        app(NotificationService::class)->notifyReviewers(
            $travel->fresh(),
            new TravelRegistrationSubmittedNotification($travel->fresh())
        );

        return redirect()
            ->route('home')
            ->with('success', 'Perbaikan terkirim. Pendaftaran kembali menunggu verifikasi Kanwil.');
    }

    private function updateCabangRevision(Request $request, RegistrationFileStash $berkas, CabangTravel $cabang, User $user)
    {
        $request->merge(array_filter([
            'pic_email' => $request->filled('pic_email') ? strtolower($request->input('pic_email')) : null,
            'pic_nomor_hp' => $request->filled('pic_nomor_hp') ? User::normalizeNomorHp($request->input('pic_nomor_hp')) : null,
            'pusat_terdaftar' => $cabang->travel_id ? '1' : '0',
            'travel_id' => $cabang->travel_id,
        ]));

        $fileMaxKb = ValidationHelper::fileMaxKb(1.5);
        $dokumenFields = array_column(CabangTravel::DOKUMEN_PENDAFTARAN, 'column');
        if (! $cabang->travel_id) {
            $dokumenFields[] = 'dokumen_sk_pusat';
        }
        $berkas->capture($request, $dokumenFields, $fileMaxKb);

        $dokumenRules = [];
        $dokumenMessages = [];
        foreach (CabangTravel::DOKUMEN_PENDAFTARAN as $meta) {
            $hasExisting = $berkas->has($meta['column']) || filled($cabang->{$meta['column']});
            $dokumenRules[$meta['column']] = ($hasExisting ? 'nullable' : 'required')
                ."|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}";
            $dokumenMessages = array_merge($dokumenMessages, ValidationHelper::fileMaxMb($meta['column'], 1.5), [
                "{$meta['column']}.mimes" => self::PESAN_FORMAT_BERKAS,
            ]);
        }

        $rules = array_merge([
            'kabupaten' => [
                'required',
                'string',
                Rule::in(NtbKabupatenMap::names()),
                Rule::unique('travel_cabang', 'kabupaten')->ignore($cabang->id_cabang, 'id_cabang')->where(fn ($query) => $query
                    ->when(
                        $cabang->travel_id,
                        fn ($q) => $q->where('travel_id', $cabang->travel_id),
                        fn ($q) => $q->whereNull('travel_id')->where(fn ($same) => $same
                            ->whereRaw(self::NAMA_RINGKAS_SQL.' = ?', [self::namaRingkas((string) $cabang->Penyelenggara)])
                            ->orWhereRaw('LOWER(TRIM(pusat)) = ?', [strtolower(trim((string) $cabang->pusat))])),
                    )
                    ->whereIn('registration_status', [
                        TravelRegistrationStatus::Pending->value,
                        TravelRegistrationStatus::MenungguKanwil->value,
                        TravelRegistrationStatus::PerluPerbaikan->value,
                        TravelRegistrationStatus::Approved->value,
                    ])),
            ],
            'SK_BA' => 'required|string|max:255',
            'tanggal' => 'required|date|before_or_equal:today',
            'pimpinan_cabang' => 'required|string|max:255',
            'alamat_cabang' => ValidationHelper::textRule(),
            'telepon' => ValidationHelper::teleponRules(),
            'pic_nama' => 'required|string|max:255',
            'pic_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'pic_nomor_hp' => ValidationHelper::nomorHpRules(uniqueInUsers: true, ignoreUserId: $user->id),
            'password' => 'nullable|string|min:8|confirmed',
        ], $dokumenRules);

        if (! $cabang->travel_id) {
            $hasSkPusat = $berkas->has('dokumen_sk_pusat') || filled($cabang->dokumen_sk_pusat);
            $rules['Penyelenggara'] = 'required|string|max:255';
            $rules['pusat'] = ['required', 'string', 'max:255'];
            $rules['pimpinan_pusat'] = 'required|string|max:255';
            $rules['alamat_pusat'] = ValidationHelper::textRule();
            $rules['dokumen_sk_pusat'] = ($hasSkPusat ? 'nullable' : 'required')."|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}";
        }

        $validated = ValidationHelper::validate($request, $rules, array_merge($dokumenMessages, [
            'kabupaten.in' => 'Pilih kabupaten/kota yang ada di NTB.',
            'kabupaten.unique' => 'Cabang travel ini di kabupaten/kota tersebut sudah pernah didaftarkan.',
            'SK_BA.required' => 'Isi nomor SK / berita acara pembukaan cabang.',
            'tanggal.before_or_equal' => 'Tanggal SK / BA tidak boleh melewati hari ini.',
            'dokumen_sk_pusat.mimes' => self::PESAN_FORMAT_BERKAS,
        ], ValidationHelper::fileMaxMb('dokumen_sk_pusat', 1.5)));

        $returnStatus = $cabang->revision_return_status
            ?: TravelRegistrationStatus::Pending->value;

        if (! in_array($returnStatus, [
            TravelRegistrationStatus::Pending->value,
            TravelRegistrationStatus::MenungguKanwil->value,
        ], true)) {
            $returnStatus = TravelRegistrationStatus::Pending->value;
        }

        DB::transaction(function () use ($berkas, $validated, $cabang, $user, $returnStatus) {
            $data = collect($validated)->only([
                'kabupaten',
                'SK_BA',
                'tanggal',
                'pimpinan_cabang',
                'alamat_cabang',
                'telepon',
                'Penyelenggara',
                'pusat',
                'pimpinan_pusat',
                'alamat_pusat',
            ])->all();

            foreach (CabangTravel::DOKUMEN_PENDAFTARAN as $type => $meta) {
                if ($berkas->has($meta['column'])) {
                    if ($cabang->{$meta['column']}) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($cabang->{$meta['column']});
                    }
                    $data[$meta['column']] = $berkas->planMove($meta['column'], "registrasi-cabang/{$type}");
                }
            }

            if (! $cabang->travel_id && $berkas->has('dokumen_sk_pusat')) {
                if ($cabang->dokumen_sk_pusat) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($cabang->dokumen_sk_pusat);
                }
                $data['dokumen_sk_pusat'] = $berkas->planMove('dokumen_sk_pusat', 'registrasi-cabang/sk_pusat');
            }

            $data['registration_status'] = TravelRegistrationStatus::from($returnStatus);
            $data['registration_notes'] = null;
            $data['revision_return_status'] = null;

            $cabang->update($data);

            $picData = [
                'nama' => $validated['pic_nama'],
                'email' => $validated['pic_email'],
                'nomor_hp' => $validated['pic_nomor_hp'],
                'kabupaten' => $cabang->kabupaten,
            ];
            if (! empty($validated['password'])) {
                $picData['password'] = Hash::make($validated['password']);
            }
            $user->update($picData);
        });

        $berkas->commitPendingMoves();
        $berkas->clear();

        $cabang = $cabang->fresh();
        app(NotificationService::class)->notifyReviewersInKabupaten(
            $cabang->kabupaten,
            new CabangRegistrationSubmittedNotification($cabang)
        );

        if ($returnStatus === TravelRegistrationStatus::MenungguKanwil->value) {
            app(NotificationService::class)->notifyAdmins(
                new CabangRegistrationSubmittedNotification($cabang)
            );
        }

        $pesan = $returnStatus === TravelRegistrationStatus::MenungguKanwil->value
            ? 'Perbaikan terkirim. Pendaftaran kembali menunggu keputusan Kanwil.'
            : 'Perbaikan terkirim. Pendaftaran kembali menunggu peninjauan Kabupaten/Kota.';

        return redirect()->route('home')->with('success', $pesan);
    }

    public function success()
    {
        return view('travel-registration.success', [
            'jenis' => session('jenis_pendaftaran', 'pusat'),
        ]);
    }
}
