<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Helpers\ValidationHelper;
use App\Models\JamaahHajiKhusus;
use App\Exports\JamaahHajiKhususExport;
use App\Support\ExportFilename;
use App\Support\JamaahExportScope;
use App\Support\JamaahListingQuery;
use App\Support\KabupatenResourceGuard;
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
    public function index(Request $request)
    {
        $user = Auth::user();
        $showTravelColumn = in_array($user->role, ['admin', 'kabupaten'], true);

        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $jamaahHajiKhusus = $this->buildHajiKhususListingQuery($request, $user)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tableBody' => view('jamaah.haji-khusus.partials.table-body', compact('jamaahHajiKhusus', 'showTravelColumn'))->render(),
                'pagination' => view('jamaah.haji-khusus.partials.pagination', compact('jamaahHajiKhusus'))->render(),
                'pagination_info' => [
                    'from' => $jamaahHajiKhusus->firstItem(),
                    'to' => $jamaahHajiKhusus->lastItem(),
                    'total' => $jamaahHajiKhusus->total(),
                    'current_page' => $jamaahHajiKhusus->currentPage(),
                    'last_page' => $jamaahHajiKhusus->lastPage(),
                ],
                'filters' => [
                    'search' => $request->get('search'),
                    'status' => $request->get('status'),
                    'travel_id' => $request->get('travel_id'),
                ],
            ]);
        }

        $travelOptions = JamaahListingQuery::travelOptionsHajiKhusus($user);

        return view('jamaah.haji-khusus.index', compact('jamaahHajiKhusus', 'showTravelColumn', 'travelOptions'));
    }

    private function buildHajiKhususListingQuery(Request $request, $user)
    {
        return JamaahListingQuery::buildHajiKhusus($request, $user);
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

        ValidationHelper::validate($request, [
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => ValidationHelper::nikRules(),
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => ValidationHelper::varcharRule(),
            'kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'required|string|size:5',
            'no_hp' => ValidationHelper::nomorHpRules(),
            'email' => 'nullable|email|max:255',
            'nama_ayah' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
            'status_pernikahan' => 'required|in:Belum Menikah,Menikah,Cerai',
            'pergi_haji' => 'nullable|in:Belum,Sudah',
            'golongan_darah' => 'required|string|max:3',
            'alergi' => ValidationHelper::varcharRule(false),
            'no_paspor' => 'nullable|string|max:255',
            'tanggal_berlaku_paspor' => 'nullable|date|after:today',
            'tempat_terbit_paspor' => 'nullable|string|max:255',
            'nomor_porsi' => ValidationHelper::nomorSpphRules(required: false),
            'tahun_pendaftaran' => 'nullable|date',
            'catatan_khusus' => ValidationHelper::textRule(false),
            'dokumen_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_paspor' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:500',
            'surat_keterangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'bukti_setor_bank' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
        ], array_merge(
            ValidationHelper::fileMaxMb('dokumen_ktp', 0.5),
            ValidationHelper::fileMaxMb('dokumen_kk', 0.5),
            ValidationHelper::fileMaxMb('dokumen_paspor', 0.5),
            ValidationHelper::fileMaxMb('dokumen_foto', 0.5),
            ValidationHelper::fileMaxMb('surat_keterangan', 0.5),
            ValidationHelper::fileMaxMb('bukti_setor_bank', 0.5),
        ));

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
                $data[$field] = StorageHelper::normalizePath($path);
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
        $jamaahHajiKhusus = JamaahHajiKhusus::with('travel')->findOrFail($id);
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
        $jamaahHajiKhusus = JamaahHajiKhusus::with('travel')->findOrFail($id);
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
        $jamaahHajiKhusus = JamaahHajiKhusus::with('travel')->findOrFail($id);
        $user = Auth::user();
        
        // Check access
        if ($user->role === 'user' && $jamaahHajiKhusus->travel_id !== $user->travel->id) {
            return redirect()->route('jamaah.haji-khusus.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        ValidationHelper::validate($request, [
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => ValidationHelper::nikRules(),
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => ValidationHelper::varcharRule(),
            'kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'required|string|size:5',
            'no_hp' => ValidationHelper::nomorHpRules(),
            'email' => 'nullable|email|max:255',
            'nama_ayah' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
            'status_pernikahan' => 'required|in:Belum Menikah,Menikah,Cerai',
            'pergi_haji' => 'nullable|in:Belum,Sudah',
            'golongan_darah' => 'required|string|max:3',
            'alergi' => ValidationHelper::varcharRule(false),
            'no_paspor' => 'nullable|string|max:255',
            'tanggal_berlaku_paspor' => 'nullable|date|after:today',
            'tempat_terbit_paspor' => 'nullable|string|max:255',
            'nomor_porsi' => ValidationHelper::nomorSpphRules(required: false),
            'tahun_pendaftaran' => 'nullable|date',
            'catatan_khusus' => ValidationHelper::textRule(false),
            'dokumen_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_paspor' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
            'dokumen_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:500',
            'surat_keterangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:500',
        ], array_merge(
            ValidationHelper::fileMaxMb('dokumen_ktp', 0.5),
            ValidationHelper::fileMaxMb('dokumen_kk', 0.5),
            ValidationHelper::fileMaxMb('dokumen_paspor', 0.5),
            ValidationHelper::fileMaxMb('dokumen_foto', 0.5),
            ValidationHelper::fileMaxMb('surat_keterangan', 0.5),
        ));

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
                $data[$field] = StorageHelper::normalizePath($path);
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
        $jamaahHajiKhusus = JamaahHajiKhusus::with('travel')->findOrFail($id);
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
        $jamaahHajiKhusus = JamaahHajiKhusus::with('travel')->findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin') {
            $message = 'Hanya Kanwil (Super Admin) yang dapat memperbarui status pendaftaran.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            abort(403, $message);
        }

        ValidationHelper::validate($request, [
            'status_pendaftaran' => 'required|in:pending,approved,rejected,completed',
        ]);

        $jamaahHajiKhusus->update([
            'status_pendaftaran' => $request->status_pendaftaran,
        ]);

        $statusText = $jamaahHajiKhusus->fresh()->getStatusText();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status pendaftaran diperbarui menjadi {$statusText}",
                'status' => $request->status_pendaftaran,
                'status_text' => $statusText,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Status jamaah haji khusus berhasil diperbarui.');
    }

    /**
     * Export jamaah haji khusus data
     */
    public function export(Request $request)
    {
        if ($request->get('format') === 'pdf') {
            return $this->exportPDF($request);
        }

        $scope = JamaahExportScope::forHajiKhusus(Auth::user(), $request);
        if ($scope['error']) {
            return back()->with('error', $scope['error']);
        }

        $filename = ExportFilename::jamaah('haji_khusus', $scope['isGlobal'], $scope['travel'], 'xlsx');

        return Excel::download(new JamaahHajiKhususExport($scope['data'], $scope['isGlobal']), $filename);
    }

    public function exportPDF(Request $request)
    {
        $scope = JamaahExportScope::forHajiKhusus(Auth::user(), $request);
        if ($scope['error']) {
            return back()->with('error', $scope['error']);
        }

        $filename = ExportFilename::jamaah('haji_khusus', $scope['isGlobal'], $scope['travel'], 'pdf');

        return $this->generatePDF($scope['data'], $scope['isGlobal'], 'haji-khusus', $filename);
    }

    private function generatePDF($data, $isGlobal, $type, $filename)
    {
        $html = $this->generatePDFHTML($data, $isGlobal, $type);
        
        $pdf = \PDF::loadHTML($html);
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
                <h1>KEMENTERIAN HAJI DAN UMROH REPUBLIK INDONESIA</h1>
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
                        <td colspan="10">PPIU: ' . ($travel->Penyelenggara ?? 'Tidak Diketahui') . ' | Kabupaten: ' . ($travel->kab_kota ?? 'Tidak Diketahui') . ' | Total: ' . $jamaahGroup->count() . ' Jamaah | Status: ' . ($travel->Status ?? 'Tidak Diketahui') . '</td>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Nama Jamaah</th>
                        <th>NIK</th>
                        <th>Alamat</th>
                        <th>No HP</th>
                        <th>Status Pendaftaran</th>
                        <th>Nomor Porsi</th>
                        <th>PPIU</th>
                        <th>Kabupaten</th>
                        <th>Status PPIU</th>
                    </tr>';

                foreach ($jamaahGroup as $index => $jamaah) {
                    $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . ($jamaah->nama_lengkap ?? '') . '</td>
                        <td>' . ($jamaah->no_ktp ?? '') . '</td>
                        <td>' . ($jamaah->alamat ?? '') . '</td>
                        <td>' . ($jamaah->no_hp ?? '') . '</td>
                        <td>' . $jamaah->getStatusText() . '</td>
                        <td>' . ($jamaah->nomor_porsi ?: '-') . '</td>
                        <td>' . ($jamaah->travel->Penyelenggara ?? 'Tidak Diketahui') . '</td>
                        <td>' . ($jamaah->travel->kab_kota ?? 'Tidak Diketahui') . '</td>
                        <td>' . ($jamaah->travel->Status ?? 'Tidak Diketahui') . '</td>
                    </tr>';
                }
                
                $html .= '</table><div class="page-break"></div>';
            }
        } else {
            $html .= '
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Jamaah</th>
                    <th>NIK</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Status Pendaftaran</th>
                    <th>Nomor Porsi</th>
                    <th>PPIU</th>
                    <th>Kabupaten</th>
                    <th>Status PPIU</th>
                </tr>';

            foreach ($data as $index => $jamaah) {
                $html .= '
                <tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . ($jamaah->nama_lengkap ?? '') . '</td>
                    <td>' . ($jamaah->no_ktp ?? '') . '</td>
                    <td>' . ($jamaah->alamat ?? '') . '</td>
                    <td>' . ($jamaah->no_hp ?? '') . '</td>
                    <td>' . $jamaah->getStatusText() . '</td>
                    <td>' . ($jamaah->nomor_porsi ?: '-') . '</td>
                    <td>' . ($jamaah->travel->Penyelenggara ?? 'Tidak Diketahui') . '</td>
                    <td>' . ($jamaah->travel->kab_kota ?? 'Tidak Diketahui') . '</td>
                    <td>' . ($jamaah->travel->Status ?? 'Tidak Diketahui') . '</td>
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
        $jamaahHajiKhusus = JamaahHajiKhusus::with('travel')->findOrFail($id);
        $user = Auth::user();
        
        // Only admin and kabupaten can verify
        if (!in_array($user->role, ['admin', 'kabupaten'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk verifikasi bukti setor.'
            ], 403);
        }

        KabupatenResourceGuard::authorizeJamaahHajiKhusus($user, $jamaahHajiKhusus);

        ValidationHelper::validate($request, [
            'status_verifikasi_bukti' => 'required|in:verified,rejected',
            'catatan_verifikasi' => ValidationHelper::textRule(false),
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
        $jamaahHajiKhusus = JamaahHajiKhusus::with('travel')->findOrFail($id);
        $user = Auth::user();
        
        // Only admin and kabupaten can assign porsi number
        if (!in_array($user->role, ['admin', 'kabupaten'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menetapkan nomor porsi.'
            ], 403);
        }

        KabupatenResourceGuard::authorizeJamaahHajiKhusus($user, $jamaahHajiKhusus);

        // Check if bukti setor is verified
        if (!$jamaahHajiKhusus->isBuktiSetorVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti setor bank harus diverifikasi terlebih dahulu.'
            ], 400);
        }

        ValidationHelper::validate($request, [
            'nomor_porsi' => ValidationHelper::nomorSpphRules(ignoreJamaahId: $jamaahHajiKhusus->id),
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
