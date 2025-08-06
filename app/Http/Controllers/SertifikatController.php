<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;
use App\Models\TravelCompany;
use App\Models\CabangTravel;
use App\Models\SertifikatSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Carbon\Carbon;
use App\Helpers\DateHelper;
use Symfony\Component\Process\Process;

class SertifikatController extends Controller
{
    public function index()
    {
        $sertifikat = Sertifikat::with(['travel', 'cabang'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('sertifikat.index', compact('sertifikat'));
    }

    public function create()
    {
        // Hanya ambil travel company yang Status-nya PPIU
        $travels = TravelCompany::where('Status', 'PPIU')->get();
        
        // Untuk cabang, ambil semua dulu (karena tidak ada kolom Status)
        // Nanti bisa difilter di view atau ditambahkan kolom Status di database
        $cabangs = CabangTravel::all();
        
        return view('sertifikat.create', compact('travels', 'cabangs'));
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
                'bulan_surat' => 'required|numeric|min:1|max:12',
                'tahun_surat' => 'required|numeric|min:2020|max:2030',
                'nomor_dokumen' => 'required|numeric|min:1',
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
                'bulan_surat' => 'required|numeric|min:1|max:12',
                'tahun_surat' => 'required|numeric|min:2020|max:2030',
                'nomor_dokumen' => 'required|numeric|min:1',
                'tanggal_tandatangan' => 'required|date',
                'jenis_lokasi' => 'required|in:pusat,cabang'
            ]);
        }

        \Log::info('Validation passed');
        $data = $request->all();
        
        // Set jenis ke PPIU karena hanya PPIU yang diakomodir
        $data['jenis'] = 'PPIU';
        \Log::info('Set jenis to PPIU');
        
        // Set travel_id atau cabang_id berdasarkan jenis lokasi
        if ($data['jenis_lokasi'] === 'pusat') {
            $data['travel_id'] = $request->travel_id;
            $data['cabang_id'] = null;
            \Log::info('Set travel_id for PUSAT:', ['travel_id' => $request->travel_id]);
        } else {
            $data['cabang_id'] = $request->cabang_id;
            $data['travel_id'] = null;
            \Log::info('Set cabang_id for CABANG:', ['cabang_id' => $request->cabang_id]);
        }
        

        
        // Format nomor surat sesuai template
        $data['nomor_surat'] = "B-{$data['nomor_surat']}/Kw.18.01/HJ.00/2/" . 
                               str_pad($data['bulan_surat'], 2, '0', STR_PAD_LEFT) . "/{$data['tahun_surat']}";
        \Log::info('Formatted nomor_surat:', ['nomor_surat' => $data['nomor_surat']]);
        
        // Format nomor dokumen menjadi 3 digit
        $data['nomor_dokumen'] = str_pad($data['nomor_dokumen'], 3, '0', STR_PAD_LEFT);
        \Log::info('Formatted nomor_dokumen:', ['nomor_dokumen' => $data['nomor_dokumen']]);

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
            // Generate QR Code using Endroid QR Code library with PNG format (no imagick needed)
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
            return back()->with('error', 'Gagal generate QR Code: ' . $e->getMessage());
        }

        // Update sertifikat with QR code path
        $sertifikat->update(['qrcode_path' => "qrcodes/qrcode_{$sertifikat->id}.png"]);
        \Log::info('Updated sertifikat with QR code path');

        // Generate Word document
        $templatePath = storage_path('app/templates/sertifhaji.docx');
        \Log::info('Template path:', ['templatePath' => $templatePath]);
        
        // Check if template exists
        if (!file_exists($templatePath)) {
            \Log::error('Template file not found');
            return back()->with('error', 'Template sertifikat tidak ditemukan');
        }
        \Log::info('Template file exists');

        $outputPath = storage_path("app/public/sertifikat/sertifikat_{$sertifikat->id}.docx");
        \Log::info('Output path:', ['outputPath' => $outputPath]);
        
        // Ensure directory exists
        if (!file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
            \Log::info('Created sertifikat directory');
        }

        try {
            \Log::info('Creating TemplateProcessor...');
            $template = new TemplateProcessor($templatePath);

            \Log::info('Setting template values...');
            $template->setValue('nama_ppiu', $sertifikat->nama_ppiu);
            $template->setValue('namakepala', $sertifikat->nama_kepala);
            $template->setValue('alamatkantor', $sertifikat->alamat);
            $template->setValue('nosert', $sertifikat->nomor_surat);
            $template->setValue('nodoc', $sertifikat->nomor_dokumen);
            
            // Extract bulan and tahun from nomor_surat
            preg_match('/\/(\d{2})\/(\d{4})$/', $sertifikat->nomor_surat, $matches);
            $bulan = $matches[1] ?? Carbon::now()->format('m');
            $tahun = $matches[2] ?? Carbon::now()->format('Y');
            
            \Log::info('Extracted bulan and tahun:', ['bulan' => $bulan, 'tahun' => $tahun]);
            
            $template->setValue('blnno', $bulan);
            $template->setValue('thnno', $tahun);
            $template->setValue('dd-mm-yyyy', DateHelper::formatIndonesia($sertifikat->tanggal_tandatangan, 'd-m-Y'));
            $template->setValue('tglterbit', DateHelper::formatIndonesia($sertifikat->tanggal_tandatangan, 'd-m-Y'));
            
            // Get signatory settings from database
            $settings = SertifikatSetting::first();
            $nama_penandatangan = $settings ? $settings->nama_penandatangan : 'Drs. H. Ahmad Hidayat, M.Pd';
            $nip_penandatangan = $settings ? $settings->nip_penandatangan : '196501011990031001';
            
            \Log::info('Signatory settings:', [
                'nama_penandatangan' => $nama_penandatangan,
                'nip_penandatangan' => $nip_penandatangan
            ]);
            
            $template->setValue('namakanwil', $nama_penandatangan);
            $template->setValue('nipkanwil', $nip_penandatangan);

            // Add QR code image
            \Log::info('Adding QR code image to template...');
            $template->setImageValue('qrcode', [
                'path' => $qrPath,
                'width' => 100,
                'height' => 100,
            ]);

            \Log::info('Saving template as Word document...');
            $template->saveAs($outputPath);
            \Log::info('Word document saved successfully');
        } catch (\Exception $e) {
            \Log::error('Word document generation failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal generate dokumen Word: ' . $e->getMessage());
        }

        // Convert Word to PDF using LibreOffice with Symfony Process
        $pdfPath = storage_path("app/public/sertifikat/sertifikat_{$sertifikat->id}.pdf");
        \Log::info('PDF path:', ['pdfPath' => $pdfPath]);
        
        try {
            \Log::info('Converting Word to PDF using LibreOffice...');
            
            // Check if LibreOffice is available
            $libreofficePath = $this->findLibreOffice();
            
            if (!$libreofficePath) {
                \Log::warning('LibreOffice not found, using development fallback');
                
                // Development fallback: Copy Word file as PDF for testing
                if (app()->environment('local')) {
                    \Log::info('Using development fallback - copying Word file as PDF');
                    copy($outputPath, $pdfPath);
                    \Log::info('Development fallback: Word file copied as PDF');
                } else {
                    return back()->with('error', 'LibreOffice tidak ditemukan. Silakan install LibreOffice terlebih dahulu.');
                }
            } else {
                // Use Symfony Process for better security and error handling
                $process = new Process([
                    $libreofficePath,
                    '--headless',
                    '--convert-to', 'pdf',
                    $outputPath,
                    '--outdir', dirname($pdfPath)
                ]);
                
                \Log::info('Executing LibreOffice process...');
                
                $process->setTimeout(60); // 60 seconds timeout
                $process->run();
                
                \Log::info('Process output:', [
                    'output' => $process->getOutput(),
                    'errorOutput' => $process->getErrorOutput(),
                    'exitCode' => $process->getExitCode()
                ]);
                
                if (!$process->isSuccessful()) {
                    throw new \Exception('LibreOffice conversion failed: ' . $process->getErrorOutput());
                }
                
                // Check if PDF was created
                if (!file_exists($pdfPath)) {
                    throw new \Exception('PDF file was not created by LibreOffice');
                }
                
                \Log::info('PDF file created successfully');
            }
        } catch (\Exception $e) {
            \Log::error('PDF conversion failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal convert ke PDF: ' . $e->getMessage());
        }

        try {
            // Update sertifikat with document path
            $sertifikat->update([
                'sertifikat_path' => "sertifikat/sertifikat_{$sertifikat->id}.docx",
                'pdf_path' => "sertifikat/sertifikat_{$sertifikat->id}.pdf"
            ]);
            \Log::info('Updated sertifikat with document paths');
        } catch (\Exception $e) {
            \Log::error('Database update failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal update database: ' . $e->getMessage());
        }

        \Log::info('=== GENERATE PDF SUCCESS ===');
        return response()->download($pdfPath)->deleteFileAfterSend(true);
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
        return response()->download($path);
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
                'sertifikat_path' => $sertifikat->sertifikat_path,
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
            
            if ($sertifikat->sertifikat_path) {
                try {
                    Storage::disk('public')->delete($sertifikat->sertifikat_path);
                    \Log::info('Word file deleted:', ['path' => $sertifikat->sertifikat_path]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete Word file:', ['error' => $e->getMessage()]);
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
    
    /**
     * Find LibreOffice executable path
     */
    private function findLibreOffice()
    {
        $possiblePaths = [
            // Windows paths
            'C:\Program Files\LibreOffice\program\soffice.exe',
            'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
            // Linux paths
            '/usr/bin/libreoffice',
            '/usr/bin/soffice',
            '/opt/libreoffice/program/soffice',
            // macOS paths
            '/Applications/LibreOffice.app/Contents/MacOS/soffice',
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        // Try to find using which command (Linux/macOS)
        $output = [];
        exec('which libreoffice 2>/dev/null', $output);
        if (!empty($output[0])) {
            return $output[0];
        }
        
        exec('which soffice 2>/dev/null', $output);
        if (!empty($output[0])) {
            return $output[0];
        }
        
        return null;
    }
} 