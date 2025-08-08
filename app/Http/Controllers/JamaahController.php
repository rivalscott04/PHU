<?php

namespace App\Http\Controllers;

use App\Models\Jamaah;
use App\Exports\JamaahExport;
use Illuminate\Http\Request;
use App\Imports\JamaahImport;
use Maatwebsite\Excel\Facades\Excel;

class JamaahController extends Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new JamaahExport, 'template_jamaah.xlsx');
    }

    public function indexHaji()
    {
        $user = auth()->user();
        
        if ($user->role === 'user') {
            // User (travel) hanya bisa melihat jamaah dari kabupatennya
            $travel = $user->travel;
            if (!$travel || $travel->Status !== 'PIHK') {
                return redirect()->route('jamaah.umrah')
                    ->with('error', 'Travel Anda tidak memiliki izin untuk mengelola jamaah haji!');
            }
            $jamaah = Jamaah::where('jenis_jamaah', 'haji')
                             ->whereHas('travel', function($query) use ($user) {
                                 $query->where('kab_kota', $user->kabupaten);
                             })->get();
            $groupedJamaah = null;
        } else if ($user->role === 'kabupaten') {
            // Kabupaten hanya bisa melihat jamaah dari kabupatennya
            $jamaah = Jamaah::where('jenis_jamaah', 'haji')
                             ->whereHas('travel', function($query) use ($user) {
                                 $query->where('kab_kota', $user->kabupaten);
                             })->get();
            $groupedJamaah = null;
        } else if ($user->role === 'admin') {
            // Admin bisa melihat semua jamaah, dikelompokkan berdasarkan travel
            $jamaah = collect(); // Empty for admin view
            $groupedJamaah = Jamaah::where('jenis_jamaah', 'haji')
                                   ->with('travel')
                                   ->get()
                                   ->groupBy('travel_id');
        } else {
            $jamaah = collect();
            $groupedJamaah = null;
        }
        
        return view('jamaah.haji.index', compact('jamaah', 'groupedJamaah'));
    }

    public function indexUmrah()
    {
        $user = auth()->user();
        
        if ($user->role === 'user') {
            // User (travel) hanya bisa melihat jamaah dari kabupatennya
            $jamaah = Jamaah::where('jenis_jamaah', 'umrah')
                             ->whereHas('travel', function($query) use ($user) {
                                 $query->where('kab_kota', $user->kabupaten);
                             })->get();
            $groupedJamaah = null;
        } else if ($user->role === 'kabupaten') {
            // Kabupaten hanya bisa melihat jamaah dari kabupatennya
            $jamaah = Jamaah::where('jenis_jamaah', 'umrah')
                             ->whereHas('travel', function($query) use ($user) {
                                 $query->where('kab_kota', $user->kabupaten);
                             })->get();
            $groupedJamaah = null;
        } else if ($user->role === 'admin') {
            // Admin bisa melihat semua jamaah, dikelompokkan berdasarkan travel
            $jamaah = collect(); // Empty for admin view
            $groupedJamaah = Jamaah::where('jenis_jamaah', 'umrah')
                                   ->with('travel')
                                   ->get()
                                   ->groupBy('travel_id');
        } else {
            $jamaah = collect();
            $groupedJamaah = null;
        }
        
        return view('jamaah.umrah.index', compact('jamaah', 'groupedJamaah'));
    }

    public function createHaji()
    {
        $user = auth()->user();
        $isAdminOrKabupaten = in_array($user->role, ['admin', 'kabupaten']);

        if (!$isAdminOrKabupaten) {
            $travel = $user->travel;
            if (!$travel || $travel->Status !== 'PIHK') {
                return redirect()->route('jamaah.umrah')
                    ->with('error', 'Travel Anda tidak memiliki izin untuk mengelola jamaah haji!');
            }
        }
        return view('jamaah.haji.create');
    }

    public function createUmrah()
    {
        return view('jamaah.umrah.create');
    }

    public function storeHaji(Request $request)
    {
        $request->validate([
            'nik' => 'required|max:16',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:15',
        ]);

        try {
            $user = auth()->user();
            $jamaahData = $request->all();
            $jamaahData['jenis_jamaah'] = 'haji';
            $jamaahData['user_id'] = $user->id;
            $jamaahData['travel_id'] = $user->travel_id;

            Jamaah::create($jamaahData);
            return redirect()->route('jamaah.haji')->with('success', 'Data jamaah haji berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function storeUmrah(Request $request)
    {
        $request->validate([
            'nik' => 'required|max:16',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:15',
        ]);

        try {
            $user = auth()->user();
            $jamaahData = $request->all();
            $jamaahData['jenis_jamaah'] = 'umrah';
            $jamaahData['user_id'] = $user->id;
            $jamaahData['travel_id'] = $user->travel_id;

            Jamaah::create($jamaahData);
            return redirect()->route('jamaah.umrah')->with('success', 'Data jamaah umrah berhasil ditambahkan!');
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

    public function destroy($id)
    {
        try {
            $jamaah = Jamaah::findOrFail($id);
            $jenisJamaah = $jamaah->jenis_jamaah;
            $jamaah->delete();

            $redirectRoute = ($jenisJamaah === 'haji') ? 'jamaah.haji' : 'jamaah.umrah';
            return redirect()->route($redirectRoute)
                ->with('success', 'Data jamaah ' . $jenisJamaah . ' berhasil dihapus!');
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
