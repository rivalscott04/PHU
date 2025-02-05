<?php

namespace App\Http\Controllers;

use App\Models\CabangTravel;
use Illuminate\Http\Request;
use App\Models\TravelCompany;
use App\Imports\CabangTravelImport;
use Maatwebsite\Excel\Facades\Excel;


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
            'Penyelenggara' => 'required|string|max:255',
            'Pusat' => 'required|string|max:255',
            'Tanggal' => 'required|date',
            'Jml_Akreditasi' => 'required|string|max:255',
            'tanggal_akreditasi' => 'required|date',
            'lembaga_akreditasi' => 'required|string|max:255',
            'Pimpinan' => 'required|string|max:255',
            'alamat_kantor_lama' => 'required|string',
            'alamat_kantor_baru' => 'required|string',
            'Telepon' => 'required|string|max:20',
            'kab_kota' => 'required|string|max:255',
            'Status' => 'required|in:PPIU,PIHK',
        ]);

        // Format data sebelum disimpan
        $validatedData['Tanggal'] = date('Y-m-d', strtotime($request->Tanggal));
        $validatedData['tanggal_akreditasi'] = date('Y-m-d', strtotime($request->tanggal_akreditasi));

        TravelCompany::create($validatedData);

        return redirect()->route('form')->with('success', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        // Temukan data berdasarkan id
        $travelCompany = TravelCompany::findOrFail($id);

        // Tampilkan view edit dengan data yang ditemukan
        return view('kanwil.editTravel', compact('travelCompany'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'Penyelenggara' => 'required|string|max:255',
            'Pusat' => 'required|string|max:255',

            'Jml_Akreditasi' => 'required|string|max:255',

            'lembaga_akreditasi' => 'required|string|max:255',
            'Pimpinan' => 'required|string|max:255',
            'alamat_kantor_lama' => 'required|string',
            'alamat_kantor_baru' => 'required|string',
            'Telepon' => 'required|string|max:20',
            'kab_kota' => 'required|string|max:255',
            'Status' => 'required|in:PPIU,PIHK',
        ]);

        // Format data sebelum disimpan
        $validatedData['Tanggal'] = date('Y-m-d', strtotime($request->Tanggal));
        $validatedData['tanggal_akreditasi'] = date('Y-m-d', strtotime($request->tanggal_akreditasi));

        // Temukan data dan update
        $travelCompany = TravelCompany::findOrFail($id);
        $travelCompany->update($validatedData);

        return redirect()->route('travel')->with('success', 'Data berhasil diperbarui.');
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
            'travel_id' => 'required|exists:travels,id',
            'kabupaten' => 'required|string|max:255',
            'pusat' => 'required|string|max:255',
            'pimpinan_pusat' => 'required|string|max:255',
            'alamat_pusat' => 'required|string',
            'SK_BA' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'pimpinan_cabang' => 'required|string|max:255',
            'alamat_cabang' => 'required|string',
            'telepon' => 'required|string|max:20',
        ]);

        // Store cabang travel data
        CabangTravel::create($validatedData);

        return redirect()->route('form.cabang_travel')->with('success', 'Cabang travel berhasil disimpan.');
    }

    public function showCabangTravel()
    {
        $data = CabangTravel::with('travel')->get();

        return view('kanwil.cabangTravel', ['data' => $data]);
    }

    public function downloadTemplate()
    {
        $filePath = public_path('template/template-travel.xlsx');

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Template file tidak ditemukan.');
        }

        return response()->download($filePath, 'template-travel.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new CabangTravelImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function downloadTemplateCabang()
    {
        $templatePath = public_path('template/template-cabang.xlsx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template file tidak ditemukan');
        }

        return response()->download($templatePath, 'template-cabang.xlsx');
    }
}
