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
use App\Helpers\StorageHelper;
use App\Helpers\ValidationHelper;
use App\Support\NtbKabupatenMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TravelRegistrationController extends Controller
{
    public function create()
    {
        return view('travel-registration.create', [
            'kabupatens' => NtbKabupatenMap::names(),
        ]);
    }

    public function store(Request $request)
    {
        $this->releaseRejectedRegistrationCredentials($request);

        $fileMaxKb = ValidationHelper::fileMaxKb(1.5);

        $rules = array_merge(ValidationHelper::travelCompanyDataRules(), [
            'pic_nama' => 'required|string|max:255',
            'pic_email' => 'required|email|max:255|unique:users,email',
            'pic_nomor_hp' => ValidationHelper::nomorHpRules(uniqueInUsers: true),
            'password' => 'required|string|min:8|confirmed',
            // Pusat hanya wajib SK izin. Sertifikat akreditasi opsional, nilainya
            // sendiri sudah diisi sebagai data pada langkah akreditasi.
            'dokumen_sk' => "required|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
            'dokumen_akreditasi' => "nullable|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
        ]);

        $validated = ValidationHelper::validate($request, $rules, array_merge(
            ValidationHelper::fileMaxMb('dokumen_sk', 1.5),
            ValidationHelper::fileMaxMb('dokumen_akreditasi', 1.5),
            [
                'kab_kota.in' => 'Pilih kabupaten/kota yang ada di NTB.',
            ]
        ));

        $travel = DB::transaction(function () use ($request, $validated) {
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
            $travelData['dokumen_sk'] = StorageHelper::normalizePath(
                $request->file('dokumen_sk')->store('registrasi-travel/sk', 'public')
            );

            if ($request->hasFile('dokumen_akreditasi')) {
                $travelData['dokumen_akreditasi'] = StorageHelper::normalizePath(
                    $request->file('dokumen_akreditasi')->store('registrasi-travel/akreditasi', 'public')
                );
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

        app(NotificationService::class)->notifyReviewers(
            $travel,
            new TravelRegistrationSubmittedNotification($travel)
        );

        return redirect()
            ->route('travel.registration.success')
            ->with('success', 'Pendaftaran berhasil dikirim. Tim Kanwil akan memverifikasi data Anda.');
    }

    public function createCabang()
    {
        return view('travel-registration.create-cabang', [
            'kabupatens' => NtbKabupatenMap::names(),
            'travels' => TravelCompany::approved()
                ->select('id', 'Penyelenggara', 'Pusat', 'Pimpinan', 'kab_kota')
                ->orderBy('Penyelenggara')
                ->get(),
        ]);
    }

    public function storeCabang(Request $request)
    {
        $this->releaseRejectedRegistrationCredentials($request);

        $fileMaxKb = ValidationHelper::fileMaxKb(1.5);
        $dokumenRules = [];
        $dokumenMessages = [];

        foreach (CabangTravel::DOKUMEN_PENDAFTARAN as $type => $meta) {
            $dokumenRules[$meta['column']] = "required|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}";
            $dokumenMessages = array_merge($dokumenMessages, ValidationHelper::fileMaxMb($meta['column'], 1.5));
        }

        $validated = ValidationHelper::validate($request, array_merge([
            // Pusat yang terdata dipilih dari daftar travel yang izinnya sudah
            // disetujui, dari sinilah nomor SK dan identitas pusat dibaca. Pusat
            // di luar NTB belum tentu terdata, jadi identitas dan SK-nya diisi
            // manual lalu diperiksa Kabupaten/Kota dan Kanwil seperti berkas lain.
            'pusat_terdaftar' => 'required|boolean',
            'travel_id' => ['exclude_unless:pusat_terdaftar,1', 'required', 'integer', Rule::exists('travels', 'id')->where('registration_status', TravelRegistrationStatus::Approved->value)],
            'Penyelenggara' => 'exclude_unless:pusat_terdaftar,0|required|string|max:255',
            'pusat' => 'exclude_unless:pusat_terdaftar,0|required|string|max:255',
            'pimpinan_pusat' => 'exclude_unless:pusat_terdaftar,0|required|string|max:255',
            'alamat_pusat' => 'exclude_unless:pusat_terdaftar,0|'.ValidationHelper::textRule(),
            'dokumen_sk_pusat' => "exclude_unless:pusat_terdaftar,0|required|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
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
                        fn ($q) => $q->whereNull('travel_id')->where('Penyelenggara', $request->input('Penyelenggara')),
                    )
                    ->whereIn('registration_status', [
                        TravelRegistrationStatus::Pending->value,
                        TravelRegistrationStatus::MenungguKanwil->value,
                        TravelRegistrationStatus::Approved->value,
                    ])),
            ],
            'SK_BA' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'pimpinan_cabang' => 'required|string|max:255',
            'alamat_cabang' => ValidationHelper::textRule(),
            'telepon' => ValidationHelper::teleponRules(),
            'pic_nama' => 'required|string|max:255',
            'pic_email' => 'required|email|max:255|unique:users,email',
            'pic_nomor_hp' => ValidationHelper::nomorHpRules(uniqueInUsers: true),
            'password' => 'required|string|min:8|confirmed',
        ], $dokumenRules), array_merge($dokumenMessages, [
            'pusat_terdaftar.required' => 'Pilih apakah travel pusat sudah terdaftar di sistem.',
            'travel_id.required' => 'Pilih travel pusat yang menaungi cabang ini.',
            'Penyelenggara.required' => 'Isi nama travel pusat.',
            'pusat.required' => 'Isi nomor SK izin PPIU pusat.',
            'pimpinan_pusat.required' => 'Isi nama pimpinan pusat.',
            'dokumen_sk_pusat.required' => 'Unggah SK izin PPIU pusat.',
            'travel_id.exists' => 'Travel pusat tidak ditemukan atau izinnya belum disetujui.',
            'kabupaten.in' => 'Pilih kabupaten/kota yang ada di NTB.',
            'kabupaten.unique' => 'Cabang travel ini di kabupaten/kota tersebut sudah pernah didaftarkan. Hubungi Kanwil bila statusnya belum juga diproses.',
            'SK_BA.required' => 'Isi nomor SK / berita acara pembukaan cabang.',
        ]));

        $cabang = DB::transaction(function () use ($request, $validated) {
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
                $data['dokumen_sk_pusat'] = StorageHelper::normalizePath(
                    $request->file('dokumen_sk_pusat')->store('registrasi-cabang/sk_pusat', 'public')
                );
            }
            $data['registration_status'] = TravelRegistrationStatus::Pending;

            foreach (CabangTravel::DOKUMEN_PENDAFTARAN as $type => $meta) {
                $data[$meta['column']] = StorageHelper::normalizePath(
                    $request->file($meta['column'])->store("registrasi-cabang/{$type}", 'public')
                );
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

        app(NotificationService::class)->notifyReviewersInKabupaten(
            $cabang->kabupaten,
            new CabangRegistrationSubmittedNotification($cabang)
        );

        return redirect()
            ->route('travel.registration.success')
            ->with('jenis_pendaftaran', 'cabang')
            ->with('success', 'Pendaftaran cabang berhasil dikirim. Kantor Kemenhaj Kabupaten/Kota akan melakukan peninjauan terlebih dahulu.');
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
