<?php

namespace App\Http\Controllers;

use App\Enums\TravelRegistrationStatus;
use App\Enums\UserRole;
use App\Models\TravelCompany;
use App\Models\User;
use App\Notifications\V2\TravelRegistrationSubmittedNotification;
use App\Services\NotificationService;
use App\Helpers\StorageHelper;
use App\Helpers\ValidationHelper;
use App\Support\NtbKabupatenMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            'dokumen_sk' => "required|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
            'dokumen_akreditasi' => "required|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
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
            ])->all();

            $travelData['registration_status'] = TravelRegistrationStatus::Pending;
            $travelData['dokumen_sk'] = StorageHelper::normalizePath(
                $request->file('dokumen_sk')->store('registrasi-travel/sk', 'public')
            );
            $travelData['dokumen_akreditasi'] = StorageHelper::normalizePath(
                $request->file('dokumen_akreditasi')->store('registrasi-travel/akreditasi', 'public')
            );

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
                'is_password_changed' => false,
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

    private function releaseRejectedRegistrationCredentials(Request $request): void
    {
        foreach (['pic_email' => 'email', 'pic_nomor_hp' => 'nomor_hp'] as $field => $column) {
            if (! $request->filled($field)) {
                continue;
            }

            User::query()
                ->where($column, $request->input($field))
                ->whereHas('travel', fn ($query) => $query->where('registration_status', TravelRegistrationStatus::Rejected))
                ->each(fn (User $user) => $user->delete());
        }
    }

    public function success()
    {
        return view('travel-registration.success');
    }
}
