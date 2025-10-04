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
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nomor_hp' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:5',
            'travel_id' => 'required|exists:travels,id',
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
            'nama' => $request->nama,
            'email' => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'travel_id' => $travel->id,
            'kabupaten' => $travel->kab_kota,
            'country' => 'Indonesia',
            'is_password_changed' => false,
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
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }
        
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
        
        // Check if user is authenticated before accessing role
        if ($user && $user->role === 'admin') {
            // Admin can see all travel companies - optimized query
            $travelData = TravelCompany::select('id', 'Penyelenggara', 'kab_kota', 'Status')->get();
        } else if ($user && $user->role === 'kabupaten') {
            // Kabupaten users can only see travel companies in their area - optimized query
            $travelData = TravelCompany::select('id', 'Penyelenggara', 'kab_kota', 'Status')
                ->where('kab_kota', $user->kabupaten)->get();
        } else {
            // Other roles or unauthenticated users see empty data
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
            'travelCount' => TravelCompany::count() + \App\Models\CabangTravel::count(),
            'jamaahHajiCount' =>  $jamaahHaji,
            'jamaahUmrahCount' =>  $jamaahUmrah,
            'airlineCount' => Bap::distinct('airlines')->count()
        ];

        // Get all travels (pusat + cabang) for form pengaduan - optimized queries
        $travelPusat = TravelCompany::select('id', 'Penyelenggara', 'kab_kota')->get();
        $travelCabang = \App\Models\CabangTravel::select('id', 'Penyelenggara', 'kabupaten')->get();
        $travels = $travelPusat->concat($travelCabang);

        return view('welcome', compact('bapData', 'travelData', 'stats', 'travels', 'travelPusat', 'travelCabang'));
    }

    public function showListTravel()
    {
        $user = auth()->user();
        
        // Check if user is authenticated before accessing role - optimized queries
        if ($user && $user->role === 'admin') {
            // Admin can see all travel companies
            $data = TravelCompany::select('id', 'Penyelenggara', 'kab_kota', 'Status')->get();
        } else if ($user && $user->role === 'kabupaten') {
            // Kabupaten users can only see travel companies in their area
            $data = TravelCompany::select('id', 'Penyelenggara', 'kab_kota', 'Status')
                ->where('kab_kota', $user->kabupaten)->get();
        } else {
            // Other roles or unauthenticated users see empty data
            $data = collect();
        }
        
        return view('travel-list', compact('data'));
    }

    public function showPublicListTravel()
    {
        // Public access - show all travel companies (pusat + cabang) without authentication - optimized queries
        $travelPusat = TravelCompany::select('id', 'Penyelenggara', 'kab_kota', 'Status', 'Pimpinan')->get();
        $travelCabang = \App\Models\CabangTravel::select('id', 'Penyelenggara', 'kabupaten', 'pimpinan_cabang')->get();
        $data = $travelPusat->concat($travelCabang);
        
        return view('travel-list-public', compact('data'));
    }
}
