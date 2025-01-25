<?php

namespace App\Http\Controllers;

use App\Models\BAP;
use Illuminate\Http\Request;

class BAPController extends Controller
{
    public function showBAP()
    {
        $data = BAP::all();

        return view('travel.pengajuanBAP', ['data' => $data]);
    }

    public function simpan(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'ppiuname' => 'required|string|max:255',
            'address_phone' => 'required|string|max:255',
            'kab_kota' => 'required|string|max:255',
            'people' => 'required|integer',
            'package' => 'required|string|max:255',
            'price' => 'required|numeric',
            'datetime' => 'required|date',
            'airlines' => 'required|string|max:255',
            'returndate' => 'required|date',
            'airlines2' => 'required|string|max:255',
        ]);

        // Debugging: Tampilkan data sebelum dikirim
        dump($request->all());

        // Simpan data ke dalam database
        BAP::create($request->all());

        // Redirect ke halaman yang diinginkan setelah data disimpan
        return redirect()->route('form')->with('success', 'Data berhasil disimpan.');
    }
}
