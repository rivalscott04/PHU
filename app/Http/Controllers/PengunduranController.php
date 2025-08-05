<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\Pengunduran;
use App\Models\Travel;
use Illuminate\Http\Request;
use App\Models\TravelCompany;
use App\Models\TravelResignation;
use Illuminate\Support\Facades\Auth;

class PengunduranController extends Controller
{

    public function index()
    {
        $pengunduran = Pengunduran::with('user')->get();
        return view('kanwil.listPengunduran', compact('pengunduran'));
    }

    public function create()
    {
        // Check if user has role 'user' (travel)
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

        $request->validate([
            'berkas_pengunduran' => 'required|file|max:500'
        ]);

        $file = $request->file('berkas_pengunduran');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/resignations', $fileName);

        Pengunduran::create([
            'user_id' => Auth::id(),
            'berkas_pengunduran' => $fileName,
            'status' => 'pending'  // Set default status
        ]);

        return redirect()->route('pengunduran.create')
            ->with('success', 'Berkas pengunduran diri berhasil dikirim');
    }
}
