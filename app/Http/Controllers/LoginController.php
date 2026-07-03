<?php

namespace App\Http\Controllers;

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
        $request->validate([
            'email_or_phone' => 'required|string',
            'password' => 'required|string',
        ], [
            'email_or_phone.required' => 'Email atau nomor HP wajib diisi.',
            'email_or_phone.string' => 'Format email atau nomor HP tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Format password tidak valid.',
        ]);

        $identifier = $request->input('email_or_phone');
        $password = $request->input('password');

        // Find user by email or phone number
        $user = User::findByEmailOrPhone($identifier);

        if ($user && Hash::check($password, $user->password)) {
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
