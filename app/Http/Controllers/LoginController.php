<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Services\AuditLogService;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        ValidationHelper::validate($request, [
            'email_or_phone' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $identifier = $request->input('email_or_phone');
        $password = $request->input('password');

        // Find user by email or phone number
        $user = User::findByEmailOrPhone($identifier);

        if ($user && Hash::check($password, $user->password)) {
            if ($user->role === UserRole::User->value && $user->travel) {
                $registrationStatus = $user->travel->registration_status;

                if ($registrationStatus?->value === 'pending') {
                    return redirect()->back()->withErrors([
                        'email_or_phone' => 'Pendaftaran travel Anda masih menunggu verifikasi Admin Kanwil. Silakan coba lagi setelah disetujui.',
                    ]);
                }

                if ($registrationStatus?->value === 'rejected') {
                    $note = $user->travel->registration_notes
                        ? ' Alasan: ' . $user->travel->registration_notes
                        : '';

                    return redirect()->back()->withErrors([
                        'email_or_phone' => 'Pendaftaran travel Anda ditolak.' . $note,
                    ]);
                }
            }

            Auth::login($user);
            $this->auditLogService->log('auth', 'login', 'masuk ke sistem', $user->id);

            return match ($user->role) {
                UserRole::User->value, UserRole::Kabupaten->value => redirect()->route('home'),
                UserRole::Pengawas->value => redirect()->route('v2.antrian.index'),
                UserRole::Pimpinan->value => redirect()->route('v2.dashboard'),
                UserRole::Admin->value => redirect()->route('home'),
                default => redirect()->intended('dashboard'),
            };
        }

        return redirect()->back()->withErrors(['email_or_phone' => 'Email/nomor HP atau password salah.']);
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($userId) {
            $this->auditLogService->log('auth', 'logout', 'keluar dari sistem', $userId);
        }

        return redirect('/');
    }
}
