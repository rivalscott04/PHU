<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Models\CabangTravel;
use App\Enums\TravelRegistrationStatus;
use Illuminate\Http\Request;
use App\Models\TravelCompany;
use App\Imports\CabangTravelImport;
use App\Exports\TravelPusatExport;
use App\Exports\TravelCabangExport;
use Illuminate\Support\Facades\Storage;
use App\Support\NtbKabupatenMap;
use Maatwebsite\Excel\Facades\Excel;


class KanwilController extends Controller
{
    // Di dalam KanwilController

    public function showFormTravel()
    {
        return view('kanwil.formTravel', [
            'kabupatens' => NtbKabupatenMap::names(),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = ValidationHelper::validate($request, ValidationHelper::travelCompanyDataRules());

        $validatedData['registration_status'] = TravelRegistrationStatus::Approved;
        $validatedData['verified_at'] = now();
        $validatedData['verified_by'] = auth()->id();

        $travelCompany = TravelCompany::create($validatedData);

        $travelCompany->setDefaultCapabilities();
        $travelCompany->description = $travelCompany->getTravelTypeDescription();
        $travelCompany->save();

        return redirect()->route('form')->with('success', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        $travelCompany = TravelCompany::with('user')->findOrFail($id);

        return view('kanwil.editTravel', [
            'travelCompany' => $travelCompany,
            'kabupatens' => NtbKabupatenMap::names(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $travelCompany = TravelCompany::with('user')->findOrFail($id);

        $validatedData = ValidationHelper::validate(
            $request,
            ValidationHelper::travelCompanyDataRules($travelCompany->id)
        );

        $travelCompany->update($validatedData);
        $travelCompany->setDefaultCapabilities();
        $travelCompany->description = $travelCompany->getTravelTypeDescription();
        $travelCompany->save();
        $travelCompany->syncPicKabupaten();

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
            ValidationHelper::validate($request, [
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

    public function showTravel(Request $request)
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all');

        $query = TravelCompany::query()
            ->with('user:id,travel_id,nama,email,nomor_hp')
            ->select(
                'id',
                'Penyelenggara',
                'Pusat',
                'Tanggal',
                'nilai_akreditasi',
                'tanggal_akreditasi',
                'lembaga_akreditasi',
                'Pimpinan',
                'alamat_kantor_lama',
                'alamat_kantor_baru',
                'Telepon',
                'Status',
                'kab_kota',
                'registration_status',
                'registration_notes',
                'dokumen_sk',
                'dokumen_akreditasi',
                'verified_at',
            );

        if ($user && $user->role === 'kabupaten') {
            $query->where('kab_kota', $user->kabupaten);
        } elseif (! $user || $user->role !== 'admin') {
            $query->whereRaw('1 = 0');
        }

        if ($filter === 'pending') {
            $query->pendingRegistration();
        } elseif ($filter === 'approved') {
            $query->approved();
        } elseif ($filter === 'rejected') {
            $query->where('registration_status', TravelRegistrationStatus::Rejected);
        }

        $data = $query->orderByDesc('created_at')->get();
        $pendingCount = TravelCompany::pendingRegistration()->count();

        return view('kanwil.travel', [
            'data' => $data,
            'filter' => $filter,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function approveRegistration($id)
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $travel = TravelCompany::with('user')->findOrFail($id);

        if (! $travel->isRegistrationPending()) {
            return redirect()->route('travel')->with('error', 'Pendaftaran ini sudah diproses sebelumnya.');
        }

        if (! $travel->hasCompleteRegistrationDocuments()) {
            return redirect()
                ->route('travel', ['filter' => 'pending'])
                ->with('error', 'Dokumen SK atau akreditasi tidak ditemukan. Minta travel mengunggah ulang sebelum disetujui.');
        }

        $travel->update([
            'registration_status' => TravelRegistrationStatus::Approved,
            'registration_notes' => null,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()
            ->route('travel', ['filter' => 'pending'])
            ->with('success', "Pendaftaran {$travel->Penyelenggara} berhasil disetujui. PIC travel sudah bisa login.");
    }

    public function rejectRegistration(Request $request, $id)
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        ValidationHelper::validate($request, [
            'registration_notes' => 'required|string|max:1000',
        ]);

        $travel = TravelCompany::findOrFail($id);

        if (! $travel->isRegistrationPending()) {
            return redirect()->route('travel')->with('error', 'Pendaftaran ini sudah diproses sebelumnya.');
        }

        $travel->update([
            'registration_status' => TravelRegistrationStatus::Rejected,
            'registration_notes' => $request->registration_notes,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        $travel->user?->delete();

        return redirect()
            ->route('travel', ['filter' => 'pending'])
            ->with('success', "Pendaftaran {$travel->Penyelenggara} ditolak.");
    }

    public function showRegistrationDocument(int $id, string $type)
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $travel = TravelCompany::findOrFail($id);

        $path = match ($type) {
            'sk' => $travel->dokumen_sk,
            'akreditasi' => $travel->dokumen_akreditasi,
            default => null,
        };

        $path = \App\Helpers\StorageHelper::normalizePath($path);

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    public function createCabangTravel()
    {
        $travels = TravelCompany::approved()->select('id', 'Penyelenggara', 'kab_kota')->orderBy('Penyelenggara')->get();

        return view('kanwil.formCabangTravel', compact('travels'));
    }

    public function storeCabangTravel(Request $request)
    {
        // Validate input
        $validatedData = ValidationHelper::validate($request, [
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
            // Admin can see all cabang travel - optimized query
            $data = CabangTravel::select('id_cabang', 'Penyelenggara', 'kabupaten', 'pusat', 'pimpinan_pusat', 'alamat_pusat', 'SK_BA', 'tanggal', 'pimpinan_cabang', 'alamat_cabang', 'telepon')->get();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten users can only see cabang travel in their area - optimized query
            $data = CabangTravel::select('id_cabang', 'Penyelenggara', 'kabupaten', 'pusat', 'pimpinan_pusat', 'alamat_pusat', 'SK_BA', 'tanggal', 'pimpinan_cabang', 'alamat_cabang', 'telepon')
                ->where('kabupaten', $user->kabupaten)->get();
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
        ValidationHelper::validate($request, [
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
        $travels = TravelCompany::approved()->select('id', 'Penyelenggara', 'kab_kota')->orderBy('Penyelenggara')->get();

        return view('kanwil.editCabangTravel', compact('cabangTravel', 'travels'));
    }

    public function updateCabangTravel(Request $request, $id_cabang)
    {
        ValidationHelper::validate($request, [
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

    /**
     * Export Travel Pusat to Excel
     */
    public function exportTravelPusat()
    {
        $user = auth()->user();
        $filename = 'Data_Travel_Pusat_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new TravelPusatExport($user), $filename);
    }

    /**
     * Export Travel Cabang to Excel
     */
    public function exportTravelCabang()
    {
        $user = auth()->user();
        $filename = 'Data_Travel_Cabang_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new TravelCabangExport($user), $filename);
    }
}
