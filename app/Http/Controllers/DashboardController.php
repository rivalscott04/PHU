<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get monthly data for the stacked column chart
        $monthlyData = collect(range(1, 12))->map(function ($month) use ($user) {
            $startDate = Carbon::create(null, $month, 1, 0, 0, 0);
            $endDate = $startDate->copy()->endOfMonth();

                    if ($user->role === 'user') {
            // For travel users, get BAP data through user_id
            return [
                'month' => $startDate->format('M'),
                'total' => DB::table('bap')
                    ->where('user_id', $user->id)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', $month)
                    ->count()
            ];
        } else {
                // For admin and kabupaten, get jamaah data
                return [
                    'month' => $startDate->format('M'),
                    'total' => DB::table('jamaah')
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', $month)
                        ->count()
                ];
            }
        });

        // Calculate percentage for radial chart (comparing to previous month)
        if ($user->role === 'user') {
            $currentMonthCount = DB::table('bap')
                ->where('user_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            $previousMonthCount = DB::table('bap')
                ->where('user_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->count();
        } else {
            $currentMonthCount = DB::table('jamaah')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            $previousMonthCount = DB::table('jamaah')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->count();
        }

        $growthPercentage = $previousMonthCount > 0
            ? round(($currentMonthCount - $previousMonthCount) / $previousMonthCount * 100)
            : 0;

        // Mengambil bulan saat ini
        $currentMonth = Carbon::now()->month;

        // Role-specific data
        switch ($user->role) {
            case 'admin':
                return $this->getAdminDashboard($user, $monthlyData, $growthPercentage, $currentMonth);
            case 'kabupaten':
                return $this->getKabupatenDashboard($user, $monthlyData, $growthPercentage, $currentMonth);
            case 'user':
                return $this->getUserDashboard($user, $monthlyData, $growthPercentage, $currentMonth);
            default:
                return $this->getKabupatenDashboard($user, $monthlyData, $growthPercentage, $currentMonth);
        }
    }

    private function getAdminDashboard($user, $monthlyData, $growthPercentage, $currentMonth)
    {
        // Admin-specific statistics
        $totalJamaah = DB::table('jamaah')->count();
        $totalBAP = DB::table('bap')->count();
        $totalTravel = DB::table('travels')->count();
        $totalUsers = DB::table('users')->count();
        $totalRevenue = DB::table('bap')
            ->select(DB::raw('SUM(people * price) as total_income'))
            ->value('total_income') ?? 0;

        return view('pages.dashboard-admin', [
            'username' => $user->username,
            'role' => $user->role,
            'totalJamaah' => $totalJamaah,
            'totalBAP' => $totalBAP,
            'totalTravel' => $totalTravel,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'monthlyData' => $monthlyData,
            'growthPercentage' => $growthPercentage,
            'bulan' => Carbon::now()->format('F Y'),
        ]);
    }

    private function getKabupatenDashboard($user, $monthlyData, $growthPercentage, $currentMonth)
    {
        // Kabupaten-specific statistics
        $jamaahHaji = DB::table('jamaah')
            ->where('jenis_jamaah', 'haji')
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $jamaahUmrah = DB::table('jamaah')
            ->where('jenis_jamaah', 'umrah')
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $bapDiajukan = DB::table('bap')
            ->where('status', 'diajukan')
            ->count();

        $bapDiproses = DB::table('bap')
            ->where('status', 'diproses')
            ->count();

        $bapSelesai = DB::table('bap')
            ->where('status', 'diterima')
            ->count();

        return view('pages.dashboard-kabupaten', [
            'username' => $user->username,
            'role' => $user->role,
            'jamaahHaji' => $jamaahHaji,
            'jamaahUmrah' => $jamaahUmrah,
            'diajukan' => $bapDiajukan,
            'diproses' => $bapDiproses,
            'selesai' => $bapSelesai,
            'monthlyData' => $monthlyData,
            'growthPercentage' => $growthPercentage,
            'bulan' => Carbon::now()->format('F Y'),
        ]);
    }

    private function getUserDashboard($user, $monthlyData, $growthPercentage, $currentMonth)
    {
        // User (travel company) specific statistics
        $myTotalBAP = DB::table('bap')
            ->where('user_id', $user->id)
            ->count();

        $myBAPDiajukan = DB::table('bap')
            ->where('user_id', $user->id)
            ->where('status', 'diajukan')
            ->count();

        $myBAPDiproses = DB::table('bap')
            ->where('user_id', $user->id)
            ->where('status', 'diproses')
            ->count();

        $myBAPSelesai = DB::table('bap')
            ->where('user_id', $user->id)
            ->where('status', 'diterima')
            ->count();

        $myTotalRevenue = DB::table('bap')
            ->where('user_id', $user->id)
            ->select(DB::raw('SUM(people * price) as total_income'))
            ->value('total_income') ?? 0;

        return view('pages.dashboard-user', [
            'username' => $user->username,
            'role' => $user->role,
            'myTotalBAP' => $myTotalBAP,
            'myBAPDiajukan' => $myBAPDiajukan,
            'myBAPDiproses' => $myBAPDiproses,
            'myBAPSelesai' => $myBAPSelesai,
            'myTotalRevenue' => $myTotalRevenue,
            'monthlyData' => $monthlyData,
            'growthPercentage' => $growthPercentage,
            'bulan' => Carbon::now()->format('F Y'),
        ]);
    }
}
