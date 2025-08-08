<?php

namespace App\Http\Controllers;

use App\Models\BAP;
use App\Models\User;
use App\Models\Jamaah;
use Illuminate\Http\Request;
use App\Models\TravelCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $defaultPassword = 'password123'; // Password Default

    public function showForm()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all travel companies
            $travels = TravelCompany::all();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten users can only see travel companies in their area
            $travels = TravelCompany::where('kab_kota', $user->kabupaten)->get();
        } else {
            // Other roles see empty data
            $travels = collect();
        }
        
        return view('kanwil.addTravelAkun', compact('travels'));
    }

    // Method untuk menambah user baru
    public function addUser(Request $request)
    {
        // Validasi input dari request
        $validator = Validator::make($request->all(), [
            'travel_id' => 'required|exists:travels,id',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:user'
        ]);

        // Jika validasi gagal, kembalikan ke form dengan pesan error
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ambil data travel untuk mendapatkan Penyelenggara
        $travel = TravelCompany::find($request->travel_id);
        if (!$travel) {
            return redirect()->back()
                ->withErrors(['travel_id' => 'Travel tidak ditemukan'])
                ->withInput();
        }

        $user = User::create([
            'username' => $travel->Penyelenggara,
            'email' => $request->email,
            'password' => $this->defaultPassword,
            'travel_id' => $travel->id
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

        return redirect()->route('bap')->with('success', 'Password Anda berhasil diperbarui.');
    }

    public function showLanding()
    {
        $bapData = Bap::select('ppiuname', 'airlines', 'datetime', 'returndate', 'airlines2')
            ->orderBy('datetime', 'asc')
            ->get();

        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all travel companies
            $travelData = TravelCompany::all();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten users can only see travel companies in their area
            $travelData = TravelCompany::where('kab_kota', $user->kabupaten)->get();
        } else {
            // Other roles see empty data
            $travelData = collect();
        }

        $jamaahHaji = DB::table('jamaah')
            ->where('jenis_jamaah', 'haji')
            ->count();

        // Menghitung jumlah jamaah umrah
        $jamaahUmrah = DB::table('jamaah')
            ->where('jenis_jamaah', 'umrah')
            ->count();

        // Add these counts
        $stats = [
            'travelCount' => TravelCompany::count(),
            'jamaahHajiCount' =>  $jamaahHaji,
            'jamaahUmrahCount' =>  $jamaahUmrah,
            'airlineCount' => Bap::distinct('airlines')->count()
        ];

        $travels = TravelCompany::all();

        return view('welcome', compact('bapData', 'travelData', 'stats', 'travels'));
    }

    public function showListTravel()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all travel companies
            $data = TravelCompany::all();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten users can only see travel companies in their area
            $data = TravelCompany::where('kab_kota', $user->kabupaten)->get();
        } else {
            // Other roles see empty data
            $data = collect();
        }
        
        return view('travel-list', compact('data'));
    }
}
