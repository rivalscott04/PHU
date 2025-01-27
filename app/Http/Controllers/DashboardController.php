<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil jumlah jamaah haji
        $jamaahHaji = DB::table('bap')->sum('people');

        // Menghitung jumlah maskapai
        $jumlahAirlines = DB::table('bap')->distinct('airlines')->count('airlines');

        // Menghitung total pendapatan
        $pendapatan = DB::table('bap')
            ->select(DB::raw('SUM(people * price) as total_income'))
            ->value('total_income');

        $user = auth()->user(); // Mengambil data user yang sedang login

        // Hitung jumlah project dengan status "diajukan"
        $bapDiajukan = DB::table('bap')->where('status', 'diajukan')->count();

        $bapDiproses = DB::table('bap')->where('status', 'diproses')->count();

        // Hitung total revenue dengan status "selesai"
        $bapSelesai = DB::table('bap')->where('status', 'selesai')->count();

        // Mengirim data ke view
        return view('pages.dashboard', [
            'jamaahHaji' => $jamaahHaji,
            'jumlahAirlines' => $jumlahAirlines,
            'pendapatan' => $pendapatan,
            'username' => $user->username,
            'role' => $user->role,
            'diajukan' => $bapDiajukan,
            'diproses' => $bapDiproses,
            'selesai' => $bapSelesai,
        ]);
    }
}
