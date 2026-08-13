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
            // Wajib untuk pendaftar baru: masa berlaku izin adalah yang membedakan
            // travel resmi dari travel bodong, dan ditampilkan ke publik.
            'license_expiry' => 'required|date|after:today',
        ]);

        $validated = ValidationHelper::validate($request, $rules, array_merge(
            ValidationHelper::fileMaxMb('dokumen_sk', 1.5),
            ValidationHelper::fileMaxMb('dokumen_akreditasi', 1.5),
            [
                'kab_kota.in' => 'Pilih kabupaten/kota yang ada di NTB.',
                'license_expiry.required' => 'Isi masa berlaku izin operasional Anda.',
                'license_expiry.after' => 'Masa berlaku izin harus melewati hari ini. Perpanjang izin sebelum mendaftar.',
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
            // Cabang harus menempel pada pusat yang izinnya sudah disetujui, karena
            // dari sinilah nomor SK pusat dan identitas pusat dibaca.
            'travel_id' => ['required', 'integer', Rule::exists('travels', 'id')->where('registration_status', TravelRegistrationStatus::Approved->value)],
            // Satu pusat hanya boleh punya satu pendaftaran cabang aktif per
            // wilayah. Tanpa ini kantor yang sama bisa didaftarkan berkali kali
            // asal memakai email PIC berbeda, dan Kabko melihat antrean ganda.
            'kabupaten' => [
                'required',
                'string',
                Rule::in(NtbKabupatenMap::names()),
                Rule::unique('travel_cabang', 'kabupaten')->where(fn ($query) => $query
                    ->where('travel_id', $request->input('travel_id'))
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
            'travel_id.required' => 'Pilih travel pusat yang menaungi cabang ini.',
            'travel_id.exists' => 'Travel pusat tidak ditemukan atau izinnya belum disetujui.',
            'kabupaten.in' => 'Pilih kabupaten/kota yang ada di NTB.',
            'kabupaten.unique' => 'Cabang travel ini di kabupaten/kota tersebut sudah pernah didaftarkan. Hubungi Kanwil bila statusnya belum juga diproses.',
            'SK_BA.required' => 'Isi nomor SK / berita acara pembukaan cabang.',
        ]));

        $cabang = DB::transaction(function () use ($request, $validated) {
            $pusat = TravelCompany::findOrFail($validated['travel_id']);

            $data = collect($validated)->only([
                'travel_id',
                'kabupaten',
                'SK_BA',
                'tanggal',
                'pimpinan_cabang',
                'alamat_cabang',
                'telepon',
            ])->all();

            // Identitas pusat tidak diketik ulang, ikut data pusat yang dipilih.
            $data['Penyelenggara'] = $pusat->Penyelenggara;
            $data['pusat'] = $pusat->Pusat;
            $data['pimpinan_pusat'] = $pusat->Pimpinan;
            $data['alamat_pusat'] = $pusat->alamat_kantor_baru ?: $pusat->alamat_kantor_lama;
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
            ->with('success', 'Pendaftaran cabang berhasil dikirim. Kantor Kemenag Kabupaten/Kota akan melakukan peninjauan terlebih dahulu.');
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
