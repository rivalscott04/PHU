<?php

namespace App\Http\Controllers;

use App\Support\HomeCommandCenter;
use App\Support\NtbKabupatenMap;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $landingRoute = $user->impersonationRedirectRoute();
        if ($landingRoute !== 'home') {
            return redirect()->route($landingRoute);
        }

        if ($user->role === 'user') {
            return $this->getUserDashboard($user);
        }

        if ($user->role === 'kabupaten') {
            return $this->getKabupatenDashboard($user);
        }

        if ($user->role === 'admin') {
            return $this->getAdminDashboard($user);
        }

        return $this->getKabupatenDashboard($user);
    }

    private function getAdminDashboard($user)
    {
        $queues = HomeCommandCenter::adminQueues();

        return view('pages.dashboard-admin', [
            'username' => $user->nama,
            'role' => $user->role,
            'queues' => $queues,
            'queueStatus' => HomeCommandCenter::overallStatus($queues),
            'summary' => HomeCommandCenter::adminSummary(),
            'alerts' => HomeCommandCenter::adminAlerts(),
            'recentPendingBap' => HomeCommandCenter::recentPendingBap(null),
            'pendingRegistrations' => HomeCommandCenter::recentPendingRegistrations(),
            'openPengaduan' => HomeCommandCenter::recentOpenPengaduan(),
            'highRiskTravels' => HomeCommandCenter::recentHighRiskTravels(),
            'upcomingDepartures' => HomeCommandCenter::upcomingDeparturesForKabupaten(null, 5),
        ]);
    }

    private function getKabupatenDashboard($user)
    {
        $kabupaten = NtbKabupatenMap::normalize($user->kabupaten) ?? $user->kabupaten;
        $queues = HomeCommandCenter::kabupatenQueues($kabupaten);
        $summary = HomeCommandCenter::kabupatenSummary($kabupaten);

        return view('pages.dashboard-kabupaten', [
            'username' => $user->nama,
            'role' => $user->role,
            'kabupaten' => $kabupaten,
            'queues' => $queues,
            'queueStatus' => HomeCommandCenter::overallStatus($queues),
            'summary' => $summary,
            'alerts' => HomeCommandCenter::kabupatenAlerts($kabupaten),
            'recentPendingBap' => HomeCommandCenter::recentPendingBap($kabupaten),
            'openPengaduan' => HomeCommandCenter::recentOpenPengaduan($kabupaten),
            'upcomingDepartures' => HomeCommandCenter::upcomingDeparturesForKabupaten($kabupaten),
        ]);
    }

    private function getUserDashboard($user)
    {
        $user->loadMissing('travel');
        $checklist = HomeCommandCenter::travelChecklist($user);

        return view('pages.dashboard-user', [
            'username' => $user->nama,
            'role' => $user->role,
            'checklist' => $checklist,
            'alerts' => HomeCommandCenter::travelAlerts($user),
            'activeBap' => HomeCommandCenter::recentActiveBap($user),
            'upcomingDepartures' => HomeCommandCenter::upcomingDepartures($user),
        ]);
    }
}
