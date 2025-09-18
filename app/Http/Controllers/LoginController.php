<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
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

            if ($user->role === 'user') {
                return redirect()->route('home');
            } elseif ($user->role === 'kabupaten') {
                return redirect()->route('home');
            } elseif ($user->role === 'admin') {
                return redirect()->route('home');
            }

            // Default redirect untuk role lainnya
            return redirect()->intended('dashboard');
        }

        return redirect()->back()->withErrors(['email_or_phone' => 'Email/nomor HP atau password salah.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
