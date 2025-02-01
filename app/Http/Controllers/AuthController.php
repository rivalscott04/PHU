<?php

namespace App\Http\Controllers;

use App\Models\BAP;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $defaultPassword = 'password123'; // Password Default

    public function showForm()
    {
        return view('kanwil.addTravelAkun');
    }

    // Method untuk menambah user baru
    public function addUser(Request $request)
    {
        // Validasi input dari request
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:user'
        ]);

        // Jika validasi gagal, kembalikan ke form dengan pesan error
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $this->defaultPassword,
        ]);


        // Kembalikan response sukses
        return redirect()->route('form.addUser')->with('success', 'Akun berhasil dibuat.');
    }

    // Method untuk menampilkan data user
    public function showUsers()
    {
        $users = User::where('role', 'user')->get();
        return view('kanwil.akunTravel', compact('users'));
    }

    // Method untuk reset password user
    public function resetPassword($id)
    {
        $user = User::find($id);
        if ($user && $user->role === 'user') {
            $user->password = 'password123';
            $user->is_password_changed = false;

            $user->save();
            return redirect()->route('travels')->with('success', 'Password berhasil direset.');
        }
        return redirect()->route('travels')->with('error', 'User tidak ditemukan atau bukan user dengan role "user".');
    }

    public function showChangePasswordForm()
    {
        return view('auth.changePassword');
    }

    // Proses update password
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->password = $request->password;
        $user->is_password_changed = true;
        $user->save();

        return redirect()->route('home')->with('success', 'Password Anda berhasil diperbarui.');
    }

    public function showLanding()
    {
        $bapData = Bap::select('ppiuname', 'airlines', 'datetime', 'returndate', 'airlines2')
            ->orderBy('datetime', 'asc')
            ->get();

        $travelData = TravelCompany::select('Penyelenggara', 'Jml_Akreditasi', 'Telepon', 'kab_kota')
            ->get();

        return view('welcome', compact('bapData', 'travelData'));
    }
}
