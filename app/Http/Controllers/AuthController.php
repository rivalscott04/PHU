<?php

namespace App\Http\Controllers;

use App\Models\BAP;
use App\Models\User;
use App\Models\Jamaah;
use App\Models\CabangTravel;
use Illuminate\Http\Request;
use App\Models\TravelCompany;
use App\Support\PublicTrustIndex;
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
        $travelPusat = TravelCompany::select('id', 'Penyelenggara', 'kab_kota', 'Status')
            ->with('riskScore')
            ->orderBy('Penyelenggara')
            ->get();

        $travels = $travelPusat->map(function ($travel) {
            $travel->trust = PublicTrustIndex::fromRiskScore($travel->riskScore);

            return $travel;
        });

        $travelCabang = CabangTravel::select('id_cabang', 'Penyelenggara', 'kabupaten')->get();

        $jamaahCounts = DB::table('jamaah')
            ->selectRaw("SUM(CASE WHEN jenis_jamaah = 'haji' THEN 1 ELSE 0 END) as haji")
            ->selectRaw("SUM(CASE WHEN jenis_jamaah = 'umrah' THEN 1 ELSE 0 END) as umrah")
            ->first();

        $stats = [
            'travelCount' => $travelPusat->count() + $travelCabang->count(),
            'jamaahHajiCount' => (int) ($jamaahCounts->haji ?? 0),
            'jamaahUmrahCount' => (int) ($jamaahCounts->umrah ?? 0),
            'airlineCount' => BAP::distinct('airlines')->count('airlines'),
        ];

        $allKabupatens = $travelPusat->pluck('kab_kota')
            ->merge($travelCabang->pluck('kabupaten'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('welcome', compact('stats', 'travels', 'travelPusat', 'travelCabang', 'allKabupatens'));
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
}
