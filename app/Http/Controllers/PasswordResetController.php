<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function request()
    {
        return view('auth.forgot-password');
    }

    public function email(Request $request)
    {
        ValidationHelper::validate($request, [
            'email' => 'required|email|max:255',
        ]);

        // Hasilnya sengaja tidak dibedakan. Kalau balasannya berbeda untuk email
        // terdaftar dan tidak terdaftar, form ini jadi alat untuk memeriksa
        // travel mana yang punya akun.
        if (! config('auth.password_reset_email_enabled')) {
            return back()->with('success', 'Hubungi petugas Kanwil untuk meminta tautan set password melalui nomor yang terdaftar pada akun Anda.');
        }

        try {
            $status = Password::sendResetLink($request->only('email'));
            if ($status === Password::RESET_LINK_SENT) {
                app(AuditLogService::class)->log('auth', 'reset_link_issued', 'Tautan set password diminta melalui email.');
            }
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            // Keep the public response identical for known and unknown accounts.
            \Illuminate\Support\Facades\Log::warning('Pengiriman email reset password gagal.');
        }

        return back()->with('success', 'Kalau email itu terdaftar, tautan set password sudah dikirim. Tautan berlaku 60 menit. Jika belum diterima, hubungi petugas Kanwil.');
    }

    public function edit(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function update(Request $request)
    {
        ValidationHelper::validate($request, [
            'token' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Ulangan password belum sama.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    // Cookie ingat saya di perangkat lain ikut mati.
                    'remember_token' => Str::random(60),
                    'is_password_changed' => true,
                ])->save();

                app(AuditLogService::class)->log(
                    'auth',
                    'password_reset',
                    "Password akun {$user->email} dibuat ulang lewat tautan",
                    $user->id
                );
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('success', 'Password berhasil dibuat. Silakan masuk memakai password baru Anda.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Tautan ini sudah kedaluwarsa atau pernah dipakai. Minta tautan baru lewat Lupa Password.']);
    }
}
