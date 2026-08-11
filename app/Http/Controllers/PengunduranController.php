<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Models\Pengunduran;
use App\Services\TravelCapabilityService;
use Illuminate\Http\Request;
use App\Support\KabupatenResourceGuard;
use App\Support\KabupatenScopeFilter;
use Illuminate\Support\Facades\Auth;

/**
 * Fitur pengunduran diri PPIU masih dimatikan lewat TravelCapabilityService
 * karena mekanisme resminya (alur persetujuan dan bentuk formulirnya) belum
 * ditetapkan. Kode ini sengaja dipertahankan, bukan sisa yang terlupakan:
 * alurnya akan dirancang ulang saat aturannya turun, jadi jangan dihapus.
 */
class PengunduranController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! TravelCapabilityService::canAccess('pengunduran')) {
                abort(404);
            }

            return $next($request);
        });
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $pengunduran = Pengunduran::with('user')->get();
        } else if ($user->role === 'kabupaten') {
            $pengunduranQuery = Pengunduran::with('user');
            KabupatenScopeFilter::applyOnTravelOrCabangUser($pengunduranQuery, $user);
            $pengunduran = $pengunduranQuery->get();
        } else {
            $pengunduran = collect();
        }

        return view('kanwil.listPengunduran', compact('pengunduran'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'user') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('travel.pengunduranPPIU');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'user') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan pengunduran diri');
        }

        ValidationHelper::validate($request, [
            'berkas_pengunduran' => 'required|file|max:500'
        ]);

        $file = $request->file('berkas_pengunduran');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/resignations', $fileName);

        Pengunduran::create([
            'user_id' => Auth::id(),
            'berkas_pengunduran' => $fileName,
            'status' => 'pending',
        ]);

        return redirect()->route('pengunduran.create')
            ->with('success', 'Berkas pengunduran diri berhasil dikirim');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['admin', 'kabupaten'], true)) {
            abort(403);
        }

        ValidationHelper::validate($request, [
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $pengunduran = Pengunduran::with('user.travel', 'user.cabang')->findOrFail($id);
        KabupatenResourceGuard::authorizePengunduran($user, $pengunduran);

        $pengunduran->update(['status' => $request->status]);

        return redirect()
            ->route('pengunduran')
            ->with('success', 'Status pengunduran berhasil diperbarui.');
    }
}
