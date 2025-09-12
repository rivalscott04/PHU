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
            'nilai_akreditasi' => 'required|string|max:255',
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

        $travelCompany = TravelCompany::create($validatedData);

        // Set default capabilities based on status
        $travelCompany->setDefaultCapabilities();
        $travelCompany->save();

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
            'nilai_akreditasi' => 'required|string|max:255',
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

        // Update capabilities based on new status
        $travelCompany->setDefaultCapabilities();
        $travelCompany->save();

        return redirect()->route('travel')->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Update travel status (PPIU/PIHK)
     */
    public function updateStatus(Request $request, $id)
    {
        \Log::info('updateStatus called', [
            'id' => $id,
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? 'unknown'
        ]);

        try {
            $request->validate([
                'Status' => 'required|in:PPIU,PIHK'
            ]);

            $travelCompany = TravelCompany::findOrFail($id);
            $oldStatus = $travelCompany->Status;
            $newStatus = $request->Status;

            \Log::info('Travel company found', [
                'travel_id' => $travelCompany->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Update status
            $travelCompany->Status = $newStatus;

            // Update capabilities based on new status
            $travelCompany->setDefaultCapabilities();
            $travelCompany->save();

            \Log::info('Travel company updated successfully', [
                'travel_id' => $travelCompany->id,
                'new_status' => $travelCompany->Status,
                'capabilities' => $travelCompany->capabilities
            ]);

            // Clear any cache that might be affecting the data
            \Cache::forget('travel_companies');

            $statusText = $newStatus === 'PIHK' ? 'PIHK (Haji & Umrah)' : 'PPIU (Umrah Only)';
            $oldStatusText = $oldStatus === 'PIHK' ? 'PIHK (Haji & Umrah)' : 'PPIU (Umrah Only)';

            return response()->json([
                'success' => true,
                'message' => "Status travel berhasil diubah dari {$oldStatusText} menjadi {$statusText}",
                'new_status' => $newStatus,
                'new_status_text' => $statusText,
                'capabilities' => $travelCompany->getAvailableServices(),
                'travel_id' => $travelCompany->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in updateStatus', [
                'errors' => $e->errors(),
                'id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Travel company not found', ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Travel company tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error in updateStatus', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showTravel()
    {
        $user = auth()->user();

        // Check if user is authenticated before accessing role
        if ($user && $user->role === 'admin') {
            // Admin can see all travel companies
            $data = TravelCompany::all();
        } else if ($user && $user->role === 'kabupaten') {
            // Kabupaten users can only see travel companies in their area
            $data = TravelCompany::where('kab_kota', $user->kabupaten)->get();
        } else {
            // Other roles or unauthenticated users see empty data
            $data = collect();
        }

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
            'Penyelenggara' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'pusat' => 'nullable|string|max:255',
            'pimpinan_pusat' => 'required|string|max:255',
            'alamat_pusat' => 'required|string',
            'SK_BA' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'pimpinan_cabang' => 'required|string|max:255',
            'alamat_cabang' => 'required|string',
            'telepon' => 'required|string|max:20',
        ]);

        CabangTravel::create($validatedData);

        return redirect()->route('cabang.travel')->with('success', 'Data cabang travel berhasil disimpan.');
    }

    public function showCabangTravel()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin can see all cabang travel
            $data = CabangTravel::all();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten users can only see cabang travel in their area
            $data = CabangTravel::where('kabupaten', $user->kabupaten)->get();
        } else {
            // Other roles see empty data
            $data = collect();
        }

        return view('kanwil.cabangTravel', ['data' => $data]);
    }

    public function downloadTemplate()
    {
        $filePath = public_path('template/template-travel.xlsx');

        // Cek apakah file exists
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Template file not found'], 404);
        }

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template-travel.xlsx"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        return response()->download($filePath, 'template-travel.xlsx', $headers);
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new CabangTravelImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function editCabangTravel($id_cabang)
    {
        $cabangTravel = CabangTravel::findOrFail($id_cabang);
        $travels = TravelCompany::all();
        return view('kanwil.editCabangTravel', compact('cabangTravel', 'travels'));
    }

    public function updateCabangTravel(Request $request, $id_cabang)
    {
        $request->validate([
            'Penyelenggara' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'pusat' => 'nullable|string|max:255',
            'pimpinan_pusat' => 'required|string|max:255',
            'alamat_pusat' => 'required|string',
            'SK_BA' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'pimpinan_cabang' => 'required|string|max:255',
            'alamat_cabang' => 'required|string',
            'telepon' => 'required|string|max:20',
        ]);

        $cabangTravel = CabangTravel::findOrFail($id_cabang);
        $cabangTravel->update($request->all());

        return redirect()->route('cabang.travel')->with('success', 'Data cabang travel berhasil diperbarui.');
    }

    public function destroyCabangTravel($id_cabang)
    {
        $cabangTravel = CabangTravel::findOrFail($id_cabang);
        $cabangTravel->delete();

        return redirect()->route('cabang.travel')->with('success', 'Data cabang travel berhasil dihapus.');
    }

    public function downloadTemplateCabang()
    {
        $filePath = public_path('template/cabang-travel.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'template-cabang-travel.xlsx');
        }

        return back()->with('error', 'Template file tidak ditemukan');
    }
}
