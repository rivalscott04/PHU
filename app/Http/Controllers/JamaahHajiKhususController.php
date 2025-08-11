<?php

namespace App\Http\Controllers;

use App\Models\JamaahHajiKhusus;
use App\Models\TravelCompany;
use App\Exports\JamaahHajiKhususExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class JamaahHajiKhususController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            // Admin melihat data dikelompokkan berdasarkan travel
            $query = JamaahHajiKhusus::with('travel');
            
            // Search functionality
            if (request()->has('search') && !empty(request('search'))) {
                $query->search(request('search'));
            }

            // Filter by status
            if (request()->has('status') && !empty(request('status'))) {
                $query->byStatus(request('status'));
            }

            $allJamaah = $query->latest()->get();
            $jamaahHajiKhusus = $allJamaah; // For statistics
            $groupedJamaahHajiKhusus = $allJamaah->groupBy('travel_id');
        } else {
            // User dan Kabupaten melihat data seperti biasa
            $query = JamaahHajiKhusus::with('travel');

            // Filter based on user role and kabupaten
            if ($user->role === 'user') {
                // User (travel) hanya bisa melihat jamaah dari kabupatennya
                if ($user->travel) {
                    $query->where('travel_id', $user->travel->id);
                }
                $query->whereHas('travel', function($q) use ($user) {
                    $q->where('kab_kota', $user->kabupaten);
                });
            } else if ($user->role === 'kabupaten') {
                // Kabupaten hanya bisa melihat jamaah dari kabupatennya
                $query->whereHas('travel', function($q) use ($user) {
                    $q->where('kab_kota', $user->kabupaten);
                });
            }

            // Search functionality
            if (request()->has('search') && !empty(request('search'))) {
                $query->search(request('search'));
            }

            // Filter by status
            if (request()->has('status') && !empty(request('status'))) {
                $query->byStatus(request('status'));
            }

            $jamaahHajiKhusus = $query->latest()->paginate(10);
            $groupedJamaahHajiKhusus = null;
        }

        return view('jamaah.haji-khusus.index', compact('jamaahHajiKhusus', 'groupedJamaahHajiKhusus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user can create haji khusus
        if ($user->role === 'user' && (!$user->travel || !$user->travel->canHandleHajiKhusus())) {
            return redirect()->route('jamaah.haji-khusus.index')
                ->with('error', 'Anda tidak memiliki akses untuk menambah jamaah haji khusus.');
        }

        return view('jamaah.haji-khusus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user can create haji khusus
        if ($user->role === 'user' && (!$user->travel || !$user->travel->canHandleHajiKhusus())) {
            return redirect()->route('jamaah.haji-khusus.index')
                ->with('error', 'Anda tidak memiliki akses untuk menambah jamaah haji khusus.');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'required|string|size:16',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'required|string|size:5',
            'no_hp' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'nama_ayah' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
            'status_pernikahan' => 'required|in:Belum Menikah,Menikah,Cerai',
            'pergi_haji' => 'nullable|in:Belum,Sudah',
            'golongan_darah' => 'required|string|max:3',
            'alergi' => 'nullable|string',
            'no_paspor' => 'nullable|string|max:255',
            'tanggal_berlaku_paspor' => 'nullable|date|after:today',
            'tempat_terbit_paspor' => 'nullable|string|max:255',
            'nomor_porsi' => 'nullable|string|max:255',
            'tahun_pendaftaran' => 'nullable|date',
            'catatan_khusus' => 'nullable|string',
            'dokumen_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_paspor' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:500',
            'surat_keterangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'bukti_setor_bank' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
        ]);

        $data = $request->all();
        $data['travel_id'] = $user->role === 'user' ? $user->travel->id : $request->travel_id;
        $data['status_pendaftaran'] = 'pending';

        // Remove nomor_porsi if user is travel (role 'user')
        if ($user->role === 'user') {
            unset($data['nomor_porsi']);
        }

        // Handle file uploads
        $fileFields = ['dokumen_ktp', 'dokumen_kk', 'dokumen_paspor', 'dokumen_foto', 'surat_keterangan', 'bukti_setor_bank'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('dokumen-haji-khusus', 'public');
                $data[$field] = $path;
            }
        }

        JamaahHajiKhusus::create($data);

        return redirect()->route('jamaah.haji-khusus.index')
            ->with('success', 'Data jamaah haji khusus berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $jamaahHajiKhusus = JamaahHajiKhusus::findOrFail($id);
        $user = Auth::user();
        
        // Check access
        if ($user->role === 'user' && $jamaahHajiKhusus->travel_id !== $user->travel->id) {
            return redirect()->route('jamaah.haji-khusus.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        return view('jamaah.haji-khusus.show', compact('jamaahHajiKhusus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $jamaahHajiKhusus = JamaahHajiKhusus::findOrFail($id);
        $user = Auth::user();
        
        // Check access
        if ($user->role === 'user' && $jamaahHajiKhusus->travel_id !== $user->travel->id) {
            return redirect()->route('jamaah.haji-khusus.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        return view('jamaah.haji-khusus.edit', compact('jamaahHajiKhusus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jamaahHajiKhusus = JamaahHajiKhusus::findOrFail($id);
        $user = Auth::user();
        
        // Check access
        if ($user->role === 'user' && $jamaahHajiKhusus->travel_id !== $user->travel->id) {
            return redirect()->route('jamaah.haji-khusus.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'required|string|size:16',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'required|string|size:5',
            'no_hp' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'nama_ayah' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
            'status_pernikahan' => 'required|in:Belum Menikah,Menikah,Cerai',
            'pergi_haji' => 'nullable|in:Belum,Sudah',
            'golongan_darah' => 'required|string|max:3',
            'alergi' => 'nullable|string',
            'no_paspor' => 'nullable|string|max:255',
            'tanggal_berlaku_paspor' => 'nullable|date|after:today',
            'tempat_terbit_paspor' => 'nullable|string|max:255',
            'nomor_porsi' => 'nullable|string|max:255',
            'tahun_pendaftaran' => 'nullable|date',
            'catatan_khusus' => 'nullable|string',
            'dokumen_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_paspor' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:500',
            'surat_keterangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
        ]);

        $data = $request->except(['dokumen_ktp', 'dokumen_kk', 'dokumen_paspor', 'dokumen_foto', 'surat_keterangan']);

        // Remove nomor_porsi if user is travel (role 'user')
        if ($user->role === 'user') {
            unset($data['nomor_porsi']);
        }

        // Handle file uploads
        $fileFields = ['dokumen_ktp', 'dokumen_kk', 'dokumen_paspor', 'dokumen_foto', 'surat_keterangan'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($jamaahHajiKhusus->$field) {
                    Storage::disk('public')->delete($jamaahHajiKhusus->$field);
                }
                
                $path = $request->file($field)->store('dokumen-haji-khusus', 'public');
                $data[$field] = $path;
            }
        }

        $jamaahHajiKhusus->update($data);

        return redirect()->route('jamaah.haji-khusus.index')
            ->with('success', 'Data jamaah haji khusus berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jamaahHajiKhusus = JamaahHajiKhusus::findOrFail($id);
        $user = Auth::user();
        
        // Check access
        if ($user->role === 'user' && $jamaahHajiKhusus->travel_id !== $user->travel->id) {
            return redirect()->route('jamaah.haji-khusus.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        // Delete associated files
        $fileFields = ['dokumen_ktp', 'dokumen_kk', 'dokumen_paspor', 'dokumen_foto', 'surat_keterangan'];
        foreach ($fileFields as $field) {
            if ($jamaahHajiKhusus->$field) {
                Storage::disk('public')->delete($jamaahHajiKhusus->$field);
            }
        }

        $jamaahHajiKhusus->delete();

        return redirect()->route('jamaah.haji-khusus.index')
            ->with('success', 'Data jamaah haji khusus berhasil dihapus.');
    }

    /**
     * Update status of jamaah haji khusus
     */
    public function updateStatus(Request $request, $id)
    {
        $jamaahHajiKhusus = JamaahHajiKhusus::findOrFail($id);
        $request->validate([
            'status_pendaftaran' => 'required|in:pending,approved,rejected,completed'
        ]);

        $jamaahHajiKhusus->update([
            'status_pendaftaran' => $request->status_pendaftaran
        ]);

        return redirect()->back()
            ->with('success', 'Status jamaah haji khusus berhasil diperbarui.');
    }

    /**
     * Export jamaah haji khusus data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $type = $request->get('type', 'global');
        $travelId = $request->get('travel_id');
        
        if ($format === 'pdf') {
            return $this->exportPDF($request);
        }
        
        if ($type === 'travel' && $travelId) {
            // Export specific travel
            $jamaah = JamaahHajiKhusus::where('travel_id', $travelId)
                                     ->with('travel')
                                     ->get();
            
            if ($jamaah->isEmpty()) {
                return back()->with('error', 'Tidak ada data jamaah haji khusus untuk travel ini.');
            }
            
            $travel = $jamaah->first()->travel ?? null;
            $filename = $travel ? 'jamaah_haji_khusus_' . str_replace(' ', '_', $travel->Penyelenggara) . '.xlsx' : 'jamaah_haji_khusus_travel.xlsx';
            
            return Excel::download(new JamaahHajiKhususExport($jamaah, false), $filename);
        } else {
            // Export global with separators
            $jamaah = JamaahHajiKhusus::with('travel')
                                     ->get()
                                     ->groupBy('travel_id');
            
            if ($jamaah->isEmpty()) {
                return back()->with('error', 'Tidak ada data jamaah haji khusus untuk diexport.');
            }
            
            $filename = 'jamaah_haji_khusus_global_' . now()->format('Y-m-d') . '.xlsx';
            
            return Excel::download(new JamaahHajiKhususExport($jamaah, true), $filename);
        }
    }

    public function exportPDF(Request $request)
    {
        $type = $request->get('type', 'global');
        $travelId = $request->get('travel_id');
        
        if ($type === 'travel' && $travelId) {
            // Export specific travel
            $jamaah = JamaahHajiKhusus::where('travel_id', $travelId)
                                     ->with('travel')
                                     ->get();
            
            if ($jamaah->isEmpty()) {
                return back()->with('error', 'Tidak ada data jamaah haji khusus untuk travel ini.');
            }
            
            $travel = $jamaah->first()->travel ?? null;
            $filename = $travel ? 'jamaah_haji_khusus_' . str_replace(' ', '_', $travel->Penyelenggara) . '.pdf' : 'jamaah_haji_khusus_travel.pdf';
            
            return $this->generatePDF($jamaah, false, 'haji-khusus', $filename);
        } else {
            // Export global with separators
            $jamaah = JamaahHajiKhusus::with('travel')
                                     ->get()
                                     ->groupBy('travel_id');
            
            if ($jamaah->isEmpty()) {
                return back()->with('error', 'Tidak ada data jamaah haji khusus untuk diexport.');
            }
            
            $filename = 'jamaah_haji_khusus_global_' . now()->format('Y-m-d') . '.pdf';
            
            return $this->generatePDF($jamaah, true, 'haji-khusus', $filename);
        }
    }

    private function generatePDF($data, $isGlobal, $type, $filename)
    {
        $html = $this->generatePDFHTML($data, $isGlobal, $type);
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }

    private function generatePDFHTML($data, $isGlobal, $type)
    {
        $title = 'Data Jamaah Haji Khusus';
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>' . $title . '</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 10px; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
                .header h1 { margin: 0; font-size: 16px; font-weight: bold; }
                .header p { margin: 5px 0; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #000; padding: 4px; text-align: left; }
                th { background-color: #34C38F; color: white; font-weight: bold; }
                .separator { background-color: #556EE6; color: white; font-weight: bold; }
                .page-break { page-break-before: always; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h1>
                <p>DIREKTORAT JENDERAL PENYELENGGARAAN HAJI DAN UMRAH</p>
                <p>DIREKTORAT PELAYANAN HAJI LUAR NEGERI</p>
                <h2>' . $title . '</h2>
                <p>Tanggal: ' . now()->format('d/m/Y') . '</p>
            </div>';

        if ($isGlobal) {
            foreach ($data as $travelId => $jamaahGroup) {
                if ($jamaahGroup->isEmpty()) {
                    continue;
                }
                
                $travel = $jamaahGroup->first()->travel;
                
                $html .= '
                <table>
                    <tr class="separator">
                        <td colspan="8">PPIU: ' . ($travel->Penyelenggara ?? 'Tidak Diketahui') . '</td>
                        <td colspan="8">Kabupaten: ' . ($travel->kab_kota ?? 'Tidak Diketahui') . '</td>
                        <td colspan="8">Total: ' . $jamaahGroup->count() . ' Jamaah</td>
                        <td colspan="6">Status: ' . ($travel->Status ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>No. KTP</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                        <th>No. HP</th>
                        <th>No. Paspor</th>
                        <th>No. SPPH</th>
                        <th>Status Bukti Setor</th>
                        <th>Status Pendaftaran</th>
                        <th>PPIU</th>
                        <th>Kabupaten</th>
                        <th>Status PPIU</th>
                        <th>Tanggal Daftar</th>
                        <th>Pekerjaan</th>
                        <th>Pendidikan</th>
                        <th>Pergi Haji</th>
                        <th>Alergi</th>
                        <th>Catatan</th>
                        <th>Nama Ayah</th>
                        <th>Email</th>
                        <th>Golongan Darah</th>
                        <th>Status Nikah</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Kota</th>
                        <th>Provinsi</th>
                        <th>Kode Pos</th>
                    </tr>';

                foreach ($jamaahGroup as $index => $jamaah) {
                    $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . ($jamaah->nama_lengkap ?? '') . '</td>
                        <td>' . ($jamaah->no_ktp ?? '') . '</td>
                        <td>' . ($jamaah->jenis_kelamin === 'L' ? 'L' : 'P') . '</td>
                        <td>' . ($jamaah->alamat ?? '') . '</td>
                        <td>' . ($jamaah->no_hp ?? '') . '</td>
                        <td>' . ($jamaah->no_paspor ?: '-') . '</td>
                        <td>' . ($jamaah->nomor_porsi ?: '-') . '</td>
                        <td>' . $jamaah->getBuktiSetorStatusText() . '</td>
                        <td>' . $jamaah->getStatusText() . '</td>
                        <td>' . ($jamaah->travel->Penyelenggara ?? 'Tidak Diketahui') . '</td>
                        <td>' . ($jamaah->travel->kab_kota ?? 'Tidak Diketahui') . '</td>
                        <td>' . ($jamaah->travel->Status ?? 'N/A') . '</td>
                        <td>' . ($jamaah->created_at ? $jamaah->created_at->format('d/m/Y') : '-') . '</td>
                        <td>' . ($jamaah->pekerjaan ?? '') . '</td>
                        <td>' . ($jamaah->pendidikan_terakhir ?? '') . '</td>
                        <td>' . ($jamaah->pergi_haji ?: '-') . '</td>
                        <td>' . ($jamaah->alergi ?: '-') . '</td>
                        <td>' . ($jamaah->catatan_khusus ?: '-') . '</td>
                        <td>' . ($jamaah->nama_ayah ?? '') . '</td>
                        <td>' . ($jamaah->email ?: '-') . '</td>
                        <td>' . ($jamaah->golongan_darah ?? '') . '</td>
                        <td>' . ($jamaah->status_pernikahan ?? '') . '</td>
                        <td>' . ($jamaah->tempat_lahir ?? '') . '</td>
                        <td>' . ($jamaah->tanggal_lahir ? $jamaah->tanggal_lahir->format('d/m/Y') : '-') . '</td>
                        <td>' . ($jamaah->kota ?? '') . '</td>
                        <td>' . ($jamaah->provinsi ?? '') . '</td>
                        <td>' . ($jamaah->kode_pos ?? '') . '</td>
                    </tr>';
                }
                
                $html .= '</table><div class="page-break"></div>';
            }
        } else {
            $html .= '
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>No. KTP</th>
                    <th>Jenis Kelamin</th>
                    <th>Alamat</th>
                    <th>No. HP</th>
                    <th>No. Paspor</th>
                    <th>No. SPPH</th>
                    <th>Status Bukti Setor</th>
                    <th>Status Pendaftaran</th>
                    <th>PPIU</th>
                    <th>Kabupaten</th>
                    <th>Status PPIU</th>
                    <th>Tanggal Daftar</th>
                    <th>Pekerjaan</th>
                    <th>Pendidikan</th>
                    <th>Pergi Haji</th>
                    <th>Alergi</th>
                    <th>Catatan</th>
                    <th>Nama Ayah</th>
                    <th>Email</th>
                    <th>Golongan Darah</th>
                    <th>Status Nikah</th>
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>Kota</th>
                    <th>Provinsi</th>
                    <th>Kode Pos</th>
                </tr>';

            foreach ($data as $index => $jamaah) {
                $html .= '
                <tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . ($jamaah->nama_lengkap ?? '') . '</td>
                    <td>' . ($jamaah->no_ktp ?? '') . '</td>
                    <td>' . ($jamaah->jenis_kelamin === 'L' ? 'L' : 'P') . '</td>
                    <td>' . ($jamaah->alamat ?? '') . '</td>
                    <td>' . ($jamaah->no_hp ?? '') . '</td>
                    <td>' . ($jamaah->no_paspor ?: '-') . '</td>
                    <td>' . ($jamaah->nomor_porsi ?: '-') . '</td>
                    <td>' . $jamaah->getBuktiSetorStatusText() . '</td>
                    <td>' . $jamaah->getStatusText() . '</td>
                    <td>' . ($jamaah->travel->Penyelenggara ?? 'Tidak Diketahui') . '</td>
                    <td>' . ($jamaah->travel->kab_kota ?? 'Tidak Diketahui') . '</td>
                    <td>' . ($jamaah->travel->Status ?? 'N/A') . '</td>
                    <td>' . ($jamaah->created_at ? $jamaah->created_at->format('d/m/Y') : '-') . '</td>
                    <td>' . ($jamaah->pekerjaan ?? '') . '</td>
                    <td>' . ($jamaah->pendidikan_terakhir ?? '') . '</td>
                    <td>' . ($jamaah->pergi_haji ?: '-') . '</td>
                    <td>' . ($jamaah->alergi ?: '-') . '</td>
                    <td>' . ($jamaah->catatan_khusus ?: '-') . '</td>
                    <td>' . ($jamaah->nama_ayah ?? '') . '</td>
                    <td>' . ($jamaah->email ?: '-') . '</td>
                    <td>' . ($jamaah->golongan_darah ?? '') . '</td>
                    <td>' . ($jamaah->status_pernikahan ?? '') . '</td>
                    <td>' . ($jamaah->tempat_lahir ?? '') . '</td>
                    <td>' . ($jamaah->tanggal_lahir ? $jamaah->tanggal_lahir->format('d/m/Y') : '-') . '</td>
                    <td>' . ($jamaah->kota ?? '') . '</td>
                    <td>' . ($jamaah->provinsi ?? '') . '</td>
                    <td>' . ($jamaah->kode_pos ?? '') . '</td>
                </tr>';
            }
            
            $html .= '</table>';
        }

        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * Verify bukti setor bank
     */
    public function verifyBuktiSetor(Request $request, $id)
    {
        $jamaahHajiKhusus = JamaahHajiKhusus::findOrFail($id);
        $user = Auth::user();
        
        // Only admin and kabupaten can verify
        if (!in_array($user->role, ['admin', 'kabupaten'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk verifikasi bukti setor.'
            ], 403);
        }

        $request->validate([
            'status_verifikasi_bukti' => 'required|in:verified,rejected',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $jamaahHajiKhusus->update([
            'status_verifikasi_bukti' => $request->status_verifikasi_bukti,
            'catatan_verifikasi' => $request->catatan_verifikasi,
            'tanggal_verifikasi' => now(),
            'verified_by' => $user->id,
        ]);

        $statusText = $request->status_verifikasi_bukti === 'verified' ? 'Terverifikasi' : 'Ditolak';

        return response()->json([
            'success' => true,
            'message' => "Bukti setor bank berhasil {$statusText}",
            'status' => $request->status_verifikasi_bukti,
            'status_text' => $statusText,
        ]);
    }

    /**
     * Assign porsi number
     */
    public function assignPorsiNumber(Request $request, $id)
    {
        $jamaahHajiKhusus = JamaahHajiKhusus::findOrFail($id);
        $user = Auth::user();
        
        // Only admin and kabupaten can assign porsi number
        if (!in_array($user->role, ['admin', 'kabupaten'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menetapkan nomor porsi.'
            ], 403);
        }

        // Check if bukti setor is verified
        if (!$jamaahHajiKhusus->isBuktiSetorVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti setor bank harus diverifikasi terlebih dahulu.'
            ], 400);
        }

        $request->validate([
            'nomor_porsi' => 'required|string|max:255|unique:jamaah_haji_khusus,nomor_porsi,' . $jamaahHajiKhusus->id,
            'tahun_pendaftaran' => 'required|string|max:4',
        ]);

        $jamaahHajiKhusus->update([
            'nomor_porsi' => $request->nomor_porsi,
            'tahun_pendaftaran' => $request->tahun_pendaftaran,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nomor porsi berhasil ditetapkan',
            'nomor_porsi' => $request->nomor_porsi,
            'tahun_pendaftaran' => $request->tahun_pendaftaran,
        ]);
    }
}
