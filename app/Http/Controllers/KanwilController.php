<?php

namespace App\Http\Controllers;

use App\Models\CabangTravel;
use Illuminate\Http\Request;
use App\Models\TravelCompany;


class KanwilController extends Controller
{
    // Di dalam KanwilController

    public function showFormTravel()
    {
        return view('kanwil.formTravel');
    }

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

    public function showTravel()
    {
        $data = TravelCompany::all();

        return view('kanwil.travel', ['data' => $data]);
    }

    public function createCabangTravel()
    {
        $travels = TravelCompany::all();
        return view('kanwil.formCabangTravel', compact('travels'));
    }

    public function storeCabangTravel(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'travel_pusat' => 'required|exists:travels,id',
            'sk_ba' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'pimpinan_cabang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
        ]);

        // Store cabang travel data
        CabangTravel::create([
            'travel_id' => $validatedData['travel_pusat'],
            'SK_BA' => $validatedData['sk_ba'],
            'tanggal' => $validatedData['tanggal'],
            'pimpinan_cabang' => $validatedData['pimpinan_cabang'],
            'alamat' => $validatedData['alamat'],
            'telepon' => $validatedData['telepon'],
        ]);

        return redirect()->route('form.cabang_travel')->with('success', 'Cabang travel berhasil disimpan.');
    }

    public function showCabangTravel()
    {
        $data = CabangTravel::with('travel')->get();

        return view('kanwil.cabangTravel', ['data' => $data]);
    }
}
