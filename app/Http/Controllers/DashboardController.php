<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get monthly data for the stacked column chart
        $monthlyData = collect(range(1, 12))->map(function ($month) {
            $startDate = Carbon::create(null, $month, 1, 0, 0, 0);
            $endDate = $startDate->copy()->endOfMonth();

            return [
                'month' => $startDate->format('M'),
                'total' => DB::table('jamaah')
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', $month)
                    ->count()
            ];
        });

        // Calculate percentage for radial chart (comparing to previous month)
        $currentMonthCount = DB::table('jamaah')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $previousMonthCount = DB::table('jamaah')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        $growthPercentage = $previousMonthCount > 0
            ? round(($currentMonthCount - $previousMonthCount) / $previousMonthCount * 100)
            : 0;
        // Mengambil bulan saat ini
        $currentMonth = Carbon::now()->month;

        // Menghitung total jamaah pada bulan ini
        $jamaah = DB::table('jamaah')
            ->whereMonth('created_at', $currentMonth)
            ->count();

        // Menghitung jumlah maskapai
        $jumlahAirlines = DB::table('bap')
            ->distinct('airlines')
            ->count('airlines');

        // Menghitung total pendapatan
        $pendapatan = DB::table('bap')
            ->select(DB::raw('SUM(people * price) as total_income'))
            ->value('total_income');

        $user = auth()->user(); // Mengambil data user yang sedang login

        // Hitung jumlah project dengan status "diajukan"
        $bapDiajukan = DB::table('bap')
            ->where('status', 'diajukan')
            ->count();

        // Hitung jumlah project dengan status "diproses"
        $bapDiproses = DB::table('bap')
            ->where('status', 'diproses')
            ->count();

        // Hitung total revenue dengan status "selesai"
        $bapSelesai = DB::table('bap')
            ->where('status', 'selesai')
            ->count();

        // Mengirim data ke view
        return view('pages.dashboard', [
            'jamaah' => $jamaah,
            'jumlahAirlines' => $jumlahAirlines,
            'pendapatan' => $pendapatan,
            'username' => $user->username,
            'role' => $user->role,
            'diajukan' => $bapDiajukan,
            'diproses' => $bapDiproses,
            'selesai' => $bapSelesai,
            'monthlyData' => $monthlyData,
            'growthPercentage' => $growthPercentage,
            'bulan' => Carbon::now()->format('F Y'), // Menambahkan nama bulan dan tahun
        ]);
    }
}
