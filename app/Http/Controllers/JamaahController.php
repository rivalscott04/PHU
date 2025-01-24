<?php

namespace App\Http\Controllers;

use App\Models\Jamaah;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\JamaahImport;
use App\Exports\JamaahExport;

class JamaahController extends Controller
{
    public function index()
    {
        $jamaah = Jamaah::all();
        return view('jamaah.index', compact('jamaah'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new JamaahImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function show(Jamaah $jamaah)
    {
        return view('jamaah.show', compact('jamaah'));
    }
    public function export()
    {
        return Excel::download(new JamaahExport, 'template_jamaah.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new JamaahExport, 'template_jamaah.xlsx');
    }
}