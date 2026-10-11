<?php

namespace App\Http\Controllers;

use App\Support\RouteAccess;

class AdminMenuController extends Controller
{
    public function pengawasan()
    {
        return $this->pintu('Pengawasan', 'Pilih satu pekerjaan. Halaman di dalamnya tetap seperti biasa.', [
            ['label' => 'Antrian pengawasan', 'hint' => 'Tugas pengawasan yang belum ditutup.', 'icon' => 'bx bx-list-check', 'route' => 'v2.antrian.index'],
            ['label' => 'Berita acara pemeriksaan', 'hint' => 'Pemeriksaan yang sedang berjalan.', 'icon' => 'bx bx-search-alt', 'route' => 'v2.pengawasan.index'],
            ['label' => 'Tindak lanjut temuan', 'hint' => 'Perbaikan dari travel yang perlu dicek.', 'icon' => 'bx bx-task', 'route' => 'v2.followup.index'],
            ['label' => 'Skor risiko', 'hint' => 'Travel yang perlu diperhatikan lebih dulu.', 'icon' => 'bx bx-error', 'route' => 'v2.risk.index'],
            ['label' => 'Profil kepatuhan', 'hint' => 'Ringkasan kepatuhan tiap PPIU.', 'icon' => 'bx bx-check-shield', 'route' => 'v2.compliance.index'],
            ['label' => 'Monitoring PPIU', 'hint' => 'Kondisi travel di seluruh NTB.', 'icon' => 'bx bx-radar', 'route' => 'v2.monitoring.index'],
            ['label' => 'Ringkasan angka', 'hint' => 'Angka pengawasan dalam satu layar.', 'icon' => 'bx bx-bar-chart-alt-2', 'route' => 'v2.dashboard'],
        ]);
    }

    public function pengaturan()
    {
        return $this->pintu('Pengaturan', 'Ini jarang dibuka. Pilih yang memang ingin diubah.', [
            ['label' => 'Kelola pengguna', 'hint' => 'Akun admin, kabupaten, pengawas, dan travel.', 'icon' => 'bx bx-user', 'route' => 'users.index'],
            ['label' => 'Kontak support', 'hint' => 'Nomor dan email yang dilihat pengguna saat kendala.', 'icon' => 'bx bx-phone', 'route' => 'settings.support.edit'],
            ['label' => 'Atur checklist', 'hint' => 'Daftar periksa yang dipakai saat pengawasan.', 'icon' => 'bx bx-list-check', 'route' => 'v2.checklist.index'],
            ['label' => 'Log aktivitas', 'hint' => 'Jejak siapa mengubah apa.', 'icon' => 'bx bx-history', 'route' => 'v2.audit-log.index'],
        ]);
    }

    /** @param  list<array{label: string, hint: string, icon: string, route: string}>  $items */
    private function pintu(string $title, string $intro, array $items)
    {
        $user = auth()->user();
        $links = [];

        foreach ($items as $item) {
            if (! $user || ! RouteAccess::canAccessRoute($user, $item['route'])) {
                continue;
            }

            $links[] = [
                'label' => $item['label'],
                'hint' => $item['hint'],
                'icon' => $item['icon'],
                'url' => route($item['route']),
            ];
        }

        return view('admin.menu.pintu', compact('title', 'intro', 'links'));
    }
}
