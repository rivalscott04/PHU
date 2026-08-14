<?php

namespace App\Http\Controllers\V2;

use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Support\NotificationUrl;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        if ($request->expectsJson()) {
            return $this->jsonSuccess([
                'notifications' => $notifications->items(),
                'unread_count' => $request->user()->unreadNotifications()->count(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'total' => $notifications->total(),
                ],
            ]);
        }

        return view('v2.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request)
    {
        $data = ValidationHelper::validate($request, [
            'id' => ['required', 'uuid'],
        ]);

        $notification = $request->user()
            ->notifications()
            ->where('id', $data['id'])
            ->firstOrFail();

        $notification->markAsRead();

        return $request->expectsJson()
            ? $this->jsonSuccess(null, 'Notifikasi ditandai sudah dibaca.')
            : back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Buka satu notifikasi: tandai sudah dibaca lalu antar ke halaman tujuannya.
     *
     * Item di lonceng dulu menaut langsung ke tujuan, sehingga notifikasi tidak
     * pernah tertandai dan angka di lonceng tidak pernah berkurang meski sudah
     * diklik. Dikerjakan di server, bukan lewat JavaScript, supaya satu klik
     * cukup satu permintaan dan tetap bekerja walau skrip gagal dimuat.
     */
    public function open(Request $request, string $notification)
    {
        $row = $request->user()
            ->notifications()
            ->where('id', $notification)
            ->firstOrFail();

        $row->markAsRead();

        $tujuan = NotificationUrl::normalize($row->data['url'] ?? null)
            ?? route('v2.notifications.index');

        return redirect()->to($tujuan);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $request->expectsJson()
            ? $this->jsonSuccess(null, 'Semua notifikasi ditandai sudah dibaca.')
            : back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
