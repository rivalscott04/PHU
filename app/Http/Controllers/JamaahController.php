<?php

namespace App\Http\Controllers;

use App\Exports\JamaahExport;
use App\Models\Jamaah;
use Illuminate\Http\Request;
use App\Imports\JamaahImport;
use Maatwebsite\Excel\Facades\Excel;

class JamaahController extends Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new JamaahExport, 'template_jamaah.xlsx');
    }

    public function index()
    {
        $jamaah = Jamaah::all();
        return view('jamaah.index', compact('jamaah'));
    }

    public function create()
    {
        return view('jamaah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:jamaahs,nik|max:16',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:15',
        ]);

        try {
            Jamaah::create($request->all());
            return redirect()->route('jamaah.index')->with('success', 'Data jamaah berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $jamaah = Jamaah::findOrFail($id);
        return view('jamaah.edit', compact('jamaah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:15',
        ]);

        try {
            $jamaah = Jamaah::findOrFail($id);
            $jamaah->update($request->only(['nama', 'alamat', 'nomor_hp']));

            return redirect()->route('jamaah.detail', $id)
                ->with('success', 'Data jamaah berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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

    public function detail($id)
    {
        $jamaah = Jamaah::findOrFail($id);
        return view('jamaah.detail', compact('jamaah'));
    }
}
