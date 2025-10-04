<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;
use App\Models\TravelCompany;
use App\Models\CabangTravel;
use App\Models\SertifikatSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Carbon\Carbon;
use App\Helpers\DateHelper;
use Dompdf\Dompdf;
use Dompdf\Options;

class SertifikatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all sertifikat
            $sertifikat = Sertifikat::with(['travel', 'cabang'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else if ($user->role === 'kabupaten') {
            // Kabupaten users can only see sertifikat from travel in their area
            $sertifikat = Sertifikat::with(['travel', 'cabang'])
                ->whereHas('travel', function($query) use ($user) {
                    $query->where('kab_kota', $user->kabupaten);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Other roles see empty data
            $sertifikat = collect();
        }

        return view('sertifikat.index', compact('sertifikat'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all PPIU travel companies - optimized queries
            $travels = TravelCompany::select('id', 'Penyelenggara', 'kab_kota', 'Status')
                ->where('Status', 'PPIU')->get();
            $cabangs = CabangTravel::select('id', 'Penyelenggara', 'kabupaten')->get();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten users can only see PPIU travel companies in their area - optimized queries
            $travels = TravelCompany::select('id', 'Penyelenggara', 'kab_kota', 'Status')
                ->where('Status', 'PPIU')
                ->where('kab_kota', $user->kabupaten)
                ->get();
            $cabangs = CabangTravel::select('id', 'Penyelenggara', 'kabupaten')
                ->where('kabupaten', $user->kabupaten)->get();
        } else {
            // Other roles see empty data
            $travels = collect();
            $cabangs = collect();
        }

        // Get next nomor surat and dokumen
        $nextNomorSurat = \App\Models\Sertifikat::getNextNomorSurat();
        $nextNomorDokumen = \App\Models\Sertifikat::getNextNomorDokumen();
        
        return view('sertifikat.create', compact('travels', 'cabangs', 'nextNomorSurat', 'nextNomorDokumen'));
    }

    public function getTravelData($id)
    {
        $travel = TravelCompany::findOrFail($id);

        // Gunakan alamat kantor baru jika ada, jika tidak gunakan alamat lama
        $alamat = $travel->alamat_kantor_baru ?: $travel->alamat_kantor_lama;

        return response()->json([
            'nama_ppiu' => $travel->Penyelenggara,
            'nama_kepala' => $travel->Pimpinan ?: ($travel->Penyelenggara . ' - Kepala'),
            'alamat' => $alamat ?: 'Alamat tidak tersedia'
        ]);
    }

    public function getCabangData($id)
    {
        $cabang = CabangTravel::findOrFail($id);
        return response()->json([
            'nama_ppiu' => $cabang->Penyelenggara,
            'nama_kepala' => $cabang->pimpinan_cabang ?: ($cabang->Penyelenggara . ' - Kepala Cabang'),
            'alamat' => $cabang->alamat_cabang ?: ($cabang->kabupaten . ' - Alamat Kantor Cabang')
        ]);
    }

    public function getNextNomor(Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        
        $nomorSurat = \App\Models\Sertifikat::getNextNomorSurat($tahun, $bulan);
        $nomorDokumen = \App\Models\Sertifikat::getNextNomorDokumen($tahun, $bulan);
        
        return response()->json([
            'nomor_surat' => $nomorSurat,
            'nomor_dokumen' => $nomorDokumen
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('=== STORE SERTIFIKAT START ===');
        \Log::info('Request data:', $request->all());

        // Validasi berdasarkan jenis lokasi
        if ($request->jenis_lokasi === 'pusat') {
            \Log::info('Validating PUSAT data');
            $request->validate([
                'travel_id' => 'required|exists:travels,id',
                'nama_ppiu' => 'required|string|max:255',
                'nama_kepala' => 'required|string|max:255',
                'alamat' => 'required|string',
                'tanggal_diterbitkan' => 'required|date',
                'nomor_surat' => 'required|numeric|min:1',
                'nomor_dokumen' => 'required|string',
                'bulan_surat' => 'required|numeric|min:1|max:12',
                'tahun_surat' => 'required|numeric|min:2020|max:2030',
                'tanggal_tandatangan' => 'required|date',
                'jenis_lokasi' => 'required|in:pusat,cabang'
            ]);
        } else {
            \Log::info('Validating CABANG data');
            $request->validate([
                'cabang_id' => 'required|exists:travel_cabang,id_cabang',
                'nama_ppiu' => 'required|string|max:255',
                'nama_kepala' => 'required|string|max:255',
                'alamat' => 'required|string',
                'tanggal_diterbitkan' => 'required|date',
                'nomor_surat' => 'required|numeric|min:1',
                'nomor_dokumen' => 'required|string',
                'bulan_surat' => 'required|numeric|min:1|max:12',
                'tahun_surat' => 'required|numeric|min:2020|max:2030',
                'tanggal_tandatangan' => 'required|date',
                'jenis_lokasi' => 'required|in:pusat,cabang'
            ]);
        }

        \Log::info('Validation passed');
        $data = $request->all();

        // Set jenis ke PPIU karena hanya PPIU yang diakomodir
        $data['jenis'] = trim('PPIU'); // Pastikan tidak ada whitespace
        \Log::info('Set jenis to PPIU:', ['jenis' => $data['jenis'], 'length' => strlen($data['jenis'])]);

        // Set travel_id atau cabang_id berdasarkan jenis lokasi
        if ($data['jenis_lokasi'] === 'pusat') {
            $data['travel_id'] = $request->travel_id;
            $data['cabang_id'] = null;
            // Hapus cabang_id dari data jika ada
            unset($data['cabang_id']);
            \Log::info('Set travel_id for PUSAT:', ['travel_id' => $request->travel_id]);
        } else {
            $data['cabang_id'] = $request->cabang_id;
            $data['travel_id'] = null;
            // Hapus travel_id dari data jika ada
            unset($data['travel_id']);
            \Log::info('Set cabang_id for CABANG:', ['cabang_id' => $request->cabang_id]);
        }

        // Format nomor surat sesuai template menggunakan data dari form
        $data['nomor_surat'] = "B-{$data['nomor_surat']}/Kw.18.01/HJ.00/2/" .
            str_pad($data['bulan_surat'], 2, '0', STR_PAD_LEFT) . "/{$data['tahun_surat']}";
        \Log::info('Formatted nomor_surat:', ['nomor_surat' => $data['nomor_surat']]);

        // Nomor dokumen sudah dalam format yang benar dari form
        \Log::info('Nomor dokumen from form:', ['nomor_dokumen' => $data['nomor_dokumen']]);

        \Log::info('Creating sertifikat with data:', $data);
        $sertifikat = Sertifikat::create($data);
        \Log::info('Sertifikat created successfully:', ['id' => $sertifikat->id, 'uuid' => $sertifikat->uuid]);

        return redirect()->route('sertifikat.index')
            ->with('success', 'Sertifikat berhasil dibuat');
    }

    public function generate($id)
    {
        \Log::info('=== GENERATE PDF START ===');
        \Log::info('Generating PDF for sertifikat ID:', ['id' => $id]);

        $sertifikat = Sertifikat::findOrFail($id);
        \Log::info('Sertifikat found:', [
            'id' => $sertifikat->id,
            'nama_ppiu' => $sertifikat->nama_ppiu,
            'nomor_surat' => $sertifikat->nomor_surat,
            'nomor_dokumen' => $sertifikat->nomor_dokumen
        ]);

        // Generate QR Code
        \Log::info('Generating QR Code...');
        $qrPath = storage_path("app/public/qrcodes/qrcode_{$sertifikat->id}.png");

        \Log::info('QR Code path:', ['qrPath' => $qrPath]);

        // Ensure directory exists
        if (!file_exists(dirname($qrPath))) {
            mkdir(dirname($qrPath), 0755, true);
            \Log::info('Created QR code directory');
        }

        try {
            // Generate QR Code using Endroid QR Code library with PNG format
            $qrCode = QrCode::create($sertifikat->getVerificationUrl())
                ->setSize(300)
                ->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Save the QR code
            $result->saveToFile($qrPath);

            \Log::info('QR Code saved successfully');
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate QR Code: ' . $e->getMessage()
            ], 500);
        }

        // Update sertifikat with QR code path
        $sertifikat->update(['qrcode_path' => "qrcodes/qrcode_{$sertifikat->id}.png"]);
        \Log::info('Updated sertifikat with QR code path');

        try {
            // Generate PDF directly using DomPDF
            \Log::info('Generating PDF using DomPDF...');

            $pdfContent = $this->generatePdfContent($sertifikat, $qrPath);

            // Configure DomPDF
            $options = new Options();
            $options->set('defaultFont', 'Times');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('chroot', [storage_path('app/public')]);

            $dompdf = new Dompdf($options);

            // Load HTML content
            $dompdf->loadHtml($pdfContent);

            // Set paper size to A4 landscape
            $dompdf->setPaper('A4', 'landscape');

            // Render PDF
            $dompdf->render();

            // Generate clean filename from travel name
            $cleanTravelName = $this->cleanTravelName($sertifikat->nama_ppiu);
            
            // Save PDF to file with travel name
            $pdfPath = storage_path("app/public/sertifikat/sertifikat_{$cleanTravelName}.pdf");

            // Ensure directory exists
            if (!file_exists(dirname($pdfPath))) {
                mkdir(dirname($pdfPath), 0755, true);
                \Log::info('Created sertifikat directory');
            }

            file_put_contents($pdfPath, $dompdf->output());
            \Log::info('PDF file created successfully with name: sertifikat_' . $cleanTravelName . '.pdf');
        } catch (\Exception $e) {
            \Log::error('PDF generation failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }

        try {
            // Generate clean filename from travel name for database
            $cleanTravelName = $this->cleanTravelName($sertifikat->nama_ppiu);
            
            // Update sertifikat with document path
            $sertifikat->update([
                'pdf_path' => "sertifikat/sertifikat_{$cleanTravelName}.pdf"
            ]);
            \Log::info('Updated sertifikat with PDF path: sertifikat_' . $cleanTravelName . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Database update failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal update database: ' . $e->getMessage()
            ], 500);
        }

        \Log::info('=== GENERATE PDF SUCCESS ===');

        // Check if request is AJAX
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sertifikat berhasil dibuat',
                'download_url' => route('sertifikat.download', $sertifikat->id),
                'view_url' => route('sertifikat.view', $sertifikat->id)
            ]);
        }

        // Fallback for non-AJAX requests (direct download)
        return response()->download($pdfPath);
    }

    /**
     * View PDF in browser
     */
    public function view($id)
    {
        \Log::info('=== VIEW PDF START ===');
        \Log::info('Viewing PDF for sertifikat ID:', ['id' => $id]);

        $sertifikat = Sertifikat::findOrFail($id);
        \Log::info('Sertifikat found:', [
            'id' => $sertifikat->id,
            'nama_ppiu' => $sertifikat->nama_ppiu,
            'pdf_path' => $sertifikat->pdf_path
        ]);

        if (!$sertifikat->pdf_path) {
            \Log::error('PDF path not found in database, redirecting to generate');
            return redirect()->route('sertifikat.generate', $id);
        }

        $path = storage_path("app/public/{$sertifikat->pdf_path}");
        \Log::info('Full PDF path:', ['path' => $path]);

        if (!file_exists($path)) {
            \Log::error('PDF file not found on server, redirecting to generate');
            return redirect()->route('sertifikat.generate', $id);
        }

        \Log::info('=== VIEW PDF SUCCESS ===');

        // Generate clean filename for browser display
        $cleanTravelName = $this->cleanTravelName($sertifikat->nama_ppiu);

        // Return PDF for inline viewing with proper filename
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="sertifikat_' . $cleanTravelName . '.pdf"'
        ]);
    }

    /**
     * Clean travel name for filename
     */
    private function cleanTravelName($travelName)
    {
        $cleanName = preg_replace('/[^a-zA-Z0-9\s]/', '', $travelName);
        $cleanName = str_replace(' ', '_', trim($cleanName));
        $cleanName = preg_replace('/_+/', '_', $cleanName);
        return $cleanName;
    }

    /**
     * Generate PDF content HTML
     */
    private function generatePdfContent($sertifikat, $qrPath)
    {
        \Log::info('Generating PDF content HTML...');

        // Extract nomor urut, bulan, dan tahun dari nomor_surat
        preg_match('/B-(\d+)\/Kw\.18\.01\/HJ\.00\/2\/(\d{2})\/(\d{4})$/', $sertifikat->nomor_surat, $matches);

        $nomor_urut = $matches[1] ?? '1';
        $bulan = $matches[2] ?? Carbon::now()->format('m');
        $tahun = $matches[3] ?? Carbon::now()->format('Y');

        // Get signatory settings from database
        $settings = SertifikatSetting::first();
        $nama_penandatangan = $settings ? $settings->nama_penandatangan : 'Drs. H. Ahmad Hidayat, M.Pd';
        $nip_penandatangan = $settings ? $settings->nip_penandatangan : '196501011990031001';

        // Convert QR code to base64
        $qrBase64 = '';
        if (file_exists($qrPath)) {
            $qrData = file_get_contents($qrPath);
            $qrBase64 = 'data:image/png;base64,' . base64_encode($qrData);
        }

        // Get background image path
        $backgroundPath = public_path('images/Picture1.png');
        $backgroundBase64 = '';
        if (file_exists($backgroundPath)) {
            $backgroundData = file_get_contents($backgroundPath);
            $backgroundBase64 = 'data:image/png;base64,' . base64_encode($backgroundData);
        }

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Times, serif;
            font-size: 14px;
            background: white url("' . $backgroundBase64 . '") no-repeat center center;
            background-size: cover;
            width: 297mm;
            height: 210mm;
            position: relative;
        }

        .container {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .doc-number-top {
            position: absolute;
            top: 20mm;
            right: 15mm;
            font-weight: bold;
            font-size: 14px;
        }

        .center-number {
            position: absolute;
            top: 85mm;
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            font-style: italic;
        }

        .main-content {
            position: absolute;
            top: 95mm;
            left: 15mm;
            right: 15mm;
            width: calc(100% - 30mm);
        }

        .left-content {
            width: 100%;
            margin-bottom: 20mm;
        }

        .keputusan {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 5mm;
            line-height: 1.4;
            text-align: justify;
        }

        .detail-table {
            width: 100%;
            margin-bottom: 5mm;
        }

        .detail-table td {
            vertical-align: top;
            font-weight: bold;
            font-size: 15px;
            padding: 1mm 0;
        }

        .label-col {
            width: 45mm;
        }

        .colon-col {
            width: 5mm;
        }

        .purpose {
            font-weight: bold;
            font-size: 15px;
            margin-top: 5mm;
        }

        .signature-section {
            position: absolute;
            bottom: 10mm;
            left: 50%;
            transform: translateX(-50%);
            width: 80mm;
            text-align: center;
        }

        .signature {
            font-size: 14px;
            font-weight: bold;
        }

        .location-date {
            text-align: left;
            margin-bottom: 2mm;
        }

        .signature-title {
            text-align: left;
            margin-bottom: 5mm;
            line-height: 1.3;
        }

        .qr-container {
            text-align: center;
        }

        .qr-img {
            width: 20mm;
            height: 20mm;
        }

        .name-signature {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
        }

        .nip {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="doc-number-top">
            No. ' . $sertifikat->nomor_dokumen . '
        </div>

        <div class="center-number">
            NOMOR: B-' . $nomor_urut . '/Kw.18.01/HJ.00/2/' . $bulan . '/' . $tahun . '
        </div>

        <div class="main-content">
            <div class="left-content">
                <div class="keputusan">
                    Berdasarkan Keputusan Kepala Kantor Wilayah Kementerian Agama Provinsi Nusa Tenggara Barat Nomor : 226 Tahun 2021 tanggal 09 Maret 2021 diberikan kepada :
                </div>

                <table class="detail-table">
                    <tr>
                        <td class="label-col">Nama PPIU</td>
                        <td class="colon-col">:</td>
                        <td>' . htmlspecialchars($sertifikat->nama_ppiu) . '</td>
                    </tr>
                    <tr>
                        <td class="label-col">Nama Kepala Cabang</td>
                        <td class="colon-col">:</td>
                        <td>' . htmlspecialchars($sertifikat->nama_kepala) . '</td>
                    </tr>
                    <tr>
                        <td class="label-col">Alamat Kantor</td>
                        <td class="colon-col">:</td>
                        <td>' . htmlspecialchars($sertifikat->alamat) . '</td>
                    </tr>
                    <tr>
                        <td class="label-col">Tanggal diterbitkannya</td>
                        <td class="colon-col">:</td>
                        <td>' . DateHelper::formatIndonesiaWithMonth($sertifikat->tanggal_diterbitkan) . '</td>
                    </tr>
                </table>

                <div class="purpose">
                    sebagai Penyelenggara Perjalanan Ibadah Umrah
                </div>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature">
                <div class="location-date">
                    Mataram, ' . DateHelper::formatIndonesiaWithMonth($sertifikat->tanggal_tandatangan) . '
                </div>

                <div class="signature-title">
                    Kepala Kantor Wilayah Kementerian Agama<br>
                    Provinsi Nusa Tenggara Barat,
                </div>

                <div class="qr-container">
                    <img src="' . $qrBase64 . '" class="qr-img" alt="QR">
                </div>

                <div class="name-signature">
                    ' . htmlspecialchars($nama_penandatangan) . '
                </div>

                <div class="nip">
                    NIP. ' . htmlspecialchars($nip_penandatangan) . '
                </div>
            </div>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    public function verifikasi($uuid)
    {
        $sertifikat = Sertifikat::where('uuid', $uuid)->firstOrFail();

        return view('sertifikat.verifikasi', compact('sertifikat'));
    }

    public function download($id)
    {
        \Log::info('=== DOWNLOAD PDF START ===');
        \Log::info('Downloading PDF for sertifikat ID:', ['id' => $id]);

        $sertifikat = Sertifikat::findOrFail($id);
        \Log::info('Sertifikat found:', [
            'id' => $sertifikat->id,
            'nama_ppiu' => $sertifikat->nama_ppiu,
            'pdf_path' => $sertifikat->pdf_path
        ]);

        if (!$sertifikat->pdf_path) {
            \Log::error('PDF path not found in database, redirecting to generate');
            return redirect()->route('sertifikat.generate', $id);
        }

        $path = storage_path("app/public/{$sertifikat->pdf_path}");
        \Log::info('Full PDF path:', ['path' => $path]);

        if (!file_exists($path)) {
            \Log::error('PDF file not found on server, redirecting to generate');
            return redirect()->route('sertifikat.generate', $id);
        }

        \Log::info('=== DOWNLOAD PDF SUCCESS ===');
        
        // Generate clean filename for download
        $cleanTravelName = preg_replace('/[^a-zA-Z0-9\s]/', '', $sertifikat->nama_ppiu);
        $cleanTravelName = str_replace(' ', '_', trim($cleanTravelName));
        $cleanTravelName = preg_replace('/_+/', '_', $cleanTravelName);
        
        return response()->download($path, "sertifikat_{$cleanTravelName}.pdf");
    }

    public function travelCertificates()
    {
        // Get certificates for the authenticated travel company
        $user = auth()->user();

        if (!$user || !$user->travel_id) {
            return redirect()->route('home')->with('error', 'Akses ditolak');
        }

        $sertifikat = Sertifikat::where('travel_id', $user->travel_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sertifikat.travel-certificates', compact('sertifikat'));
    }

    public function destroy($id)
    {
        try {
            \Log::info('=== DELETE SERTIFIKAT START ===');
            \Log::info('Deleting sertifikat ID:', ['id' => $id]);

            $sertifikat = Sertifikat::findOrFail($id);
            \Log::info('Sertifikat found:', [
                'id' => $sertifikat->id,
                'nama_ppiu' => $sertifikat->nama_ppiu,
                'qrcode_path' => $sertifikat->qrcode_path,
                'pdf_path' => $sertifikat->pdf_path
            ]);

            // Delete associated files
            if ($sertifikat->qrcode_path) {
                try {
                    Storage::disk('public')->delete($sertifikat->qrcode_path);
                    \Log::info('QR code file deleted:', ['path' => $sertifikat->qrcode_path]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete QR code file:', ['error' => $e->getMessage()]);
                }
            }

            if ($sertifikat->pdf_path) {
                try {
                    Storage::disk('public')->delete($sertifikat->pdf_path);
                    \Log::info('PDF file deleted:', ['path' => $sertifikat->pdf_path]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete PDF file:', ['error' => $e->getMessage()]);
                }
            }

            // Delete the database record
            $sertifikat->delete();
            \Log::info('Sertifikat record deleted from database');

            \Log::info('=== DELETE SERTIFIKAT SUCCESS ===');
            return redirect()->route('sertifikat.index')
                ->with('success', 'Sertifikat berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Delete sertifikat failed:', ['error' => $e->getMessage()]);
            return redirect()->route('sertifikat.index')
                ->with('error', 'Gagal menghapus sertifikat: ' . $e->getMessage());
        }
    }

    public function getSettings()
    {
        $settings = SertifikatSetting::first();
        return response()->json($settings);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'nama_penandatangan' => 'required|string|max:255',
            'nip_penandatangan' => 'required|string|max:255',
        ]);

        $settings = SertifikatSetting::first();

        if (!$settings) {
            $settings = new SertifikatSetting();
        }

        $settings->nama_penandatangan = $request->nama_penandatangan;
        $settings->nip_penandatangan = $request->nip_penandatangan;
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan penandatangan berhasil disimpan'
        ]);
    }
}
