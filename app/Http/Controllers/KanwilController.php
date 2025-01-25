<?php

namespace App\Http\Controllers;

use App\Models\TravelCompany;
use Illuminate\Http\Request;


class KanwilController extends Controller
{
    // Di dalam KanwilController



    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'penyelenggara' => 'required|string|max:255',
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date',
            'akreditasi' => 'required|string|max:255',
            'tanggal_akreditasi' => 'required|date',
            'lembaga_akreditasi' => 'required|string|max:255',
            'pimpinan' => 'required|string|max:255',
            'alamat_kantor_lama' => 'required|string',
            'alamat_kantor_baru' => 'required|string',
            'telepon' => 'required|string|max:20',
            'kab_kota' => 'required|string|max:255',
        ]);

        $validatedData['status'] = 'diajukan';

        TravelCompany::create($validatedData);

        return redirect()->route('form')->with('success', 'Data berhasil disimpan.');
    }

    public function showPengajuan()
    {
        $data = TravelCompany::all();

        return view('kanwil.pengajuanBAP', ['data' => $data]);
    }

    public function updateStatus(Request $request, $id)
    {

        $validatedData = $request->validate([
            'status' => 'required|string|in:diajukan,diproses,diterima',
        ]);

        $item = TravelCompany::findOrFail($id);
        $item->update(['status' => $validatedData['status']]);

        return redirect()->route('pengajuan')->with('success', 'Status berhasil diperbarui.');
    }
}
