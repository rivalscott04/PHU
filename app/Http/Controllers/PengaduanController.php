<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use App\Models\TravelCompany;

class PengaduanController extends Controller
{
    public function create()
    {
        $travels = TravelCompany::all();
        return view('pengaduan.create', compact('travels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pengadu' => 'required',
            'travels_id' => 'required|exists:travels,id',
            'hal_aduan' => 'required',
            'berkas_aduan' => 'nullable|file|max:2048',
        ]);

        $berkasPath = null;
        if ($request->hasFile('berkas_aduan')) {
            $berkasPath = $request->file('berkas_aduan')->store('berkas_aduan', 'public');
        }

        Pengaduan::create([
            'nama_pengadu' => $request->nama_pengadu,
            'travels_id' => $request->travels_id,
            'hal_aduan' => $request->hal_aduan,
            'berkas_aduan' => $berkasPath,
        ]);

        return redirect()->back()->with('success', 'Pengaduan berhasil dikirim!');
    }

    public function index()
    {
        $pengaduan = Pengaduan::with('travel')->get();
        return view('pengaduan.index', compact('pengaduan'));
    }

    public function detail($id)
    {
        $pengaduan = Pengaduan::with('travel')->findOrFail($id);
        return view('pengaduan.detail', compact('pengaduan'));
    }
}
