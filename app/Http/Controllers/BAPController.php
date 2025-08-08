<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BAP;
use App\Models\Jamaah;
use Illuminate\Http\Request;
use App\Models\TravelCompany;

class BAPController extends Controller
{
    public function showFormBAP()
    {
        $user = auth()->user();
        $jamaahCount = 0;
        $travelData = null;

        // Handle admin role
        if ($user->role === 'admin') {
            $jamaahCount = Jamaah::count();
            if ($jamaahCount == 0) {
                return redirect()->back()->with('error', 'Tidak bisa menambahkan, karena belum ada data jamaah.');
            }
        }
        // Handle non-admin/kabupaten users
        else if ($user->role !== 'kabupaten') {
            $travelData = TravelCompany::find($user->travel_id);

            if (!$travelData) {
                return redirect()->back()->with('error', 'Data travel tidak ditemukan.');
            }

            if ($travelData->Status === 'PPIU') {
                $jamaahCount = Jamaah::where('jenis_jamaah', 'umrah')->count();
            } else if ($travelData->Status === 'PIHK') {
                $jamaahCount = Jamaah::count();
            }

            if ($jamaahCount == 0) {
                return redirect()->back()->with('error', 'Tidak bisa menambahkan, karena belum ada data jamaah.');
            }
        }

        $ppiuList = TravelCompany::select('penyelenggara')->distinct()->get();

        return view('travel.pengajuanBAP', compact('ppiuList', 'jamaahCount', 'travelData'));
    }

    public function detail($id)
    {
        $data = BAP::findOrFail($id);

        return view('travel.detailBAP', ['data' => $data]);
    }

    public function printBAP($id)
    {
        $data = BAP::findOrFail($id);
        
        // Format tanggal untuk tampilan
        $formattedDate = \Carbon\Carbon::parse($data->datetime)->translatedFormat('d F Y');
        $formattedReturnDate = \Carbon\Carbon::parse($data->returndate)->translatedFormat('d F Y');
        
        // Generate QR Code untuk tanda tangan digital petugas
        $qrPath = null;
        if ($data->status === 'diterima') {
            try {
                // Generate QR Code menggunakan Endroid QR Code library
                $qrCode = \Endroid\QrCode\QrCode::create(json_encode([
                    'nomor_surat' => $data->nomor_surat,
                    'nama_petugas' => auth()->user()->firstname . ' ' . auth()->user()->lastname,
                    'jabatan_petugas' => 'Petugas Satgas Umrah',
                    'tanggal_tanda_tangan' => now()->format('Y-m-d H:i:s'),
                    'status_dokumen' => $data->status,
                    'verifikasi_digital' => true,
                    'hash_verifikasi' => hash('sha256', $data->id . $data->nomor_surat . auth()->id())
                ]))
                ->setSize(200)
                ->setMargin(10);

                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);

                // Save QR Code to file in BAP_Sign folder
                $qrPath = storage_path("app/public/BAP_Sign/bap_qrcode_{$data->id}.png");
                
                // Ensure directory exists
                if (!file_exists(dirname($qrPath))) {
                    mkdir(dirname($qrPath), 0755, true);
                }

                $result->saveToFile($qrPath);
                
                // Convert to base64 for display
                $qrCodeData = base64_encode(file_get_contents($qrPath));
                
            } catch (\Exception $e) {
                \Log::error('QR Code generation failed:', ['error' => $e->getMessage()]);
                $qrCodeData = null;
            }
        } else {
            $qrCodeData = null;
        }
        
        return view('travel.printBAP', compact('data', 'qrCodeData', 'formattedDate', 'formattedReturnDate'));
    }



    public function nomorKata($number)
    {
        $words = array(
            0 => 'Nol',
            1 => 'Satu',
            2 => 'Dua',
            3 => 'Tiga',
            4 => 'Empat',
            5 => 'Lima',
            6 => 'Enam',
            7 => 'Tujuh',
            8 => 'Delapan',
            9 => 'Sembilan',
            10 => 'Sepuluh',
            11 => 'Sebelas',
            12 => 'Dua Belas',
            13 => 'Tiga Belas',
            14 => 'Empat Belas',
            15 => 'Lima Belas',
            16 => 'Enam Belas',
            17 => 'Tujuh Belas',
            18 => 'Delapan Belas',
            19 => 'Sembilan Belas',
            20 => 'Dua Puluh',
            30 => 'Tiga Puluh',
            40 => 'Empat Puluh',
            50 => 'Lima Puluh',
            60 => 'Enam Puluh',
            70 => 'Tujuh Puluh',
            80 => 'Delapan Puluh',
            90 => 'Sembilan Puluh',
            100 => 'Seratus',
            200 => 'Dua Ratus',
            300 => 'Tiga Ratus',
            400 => 'Empat Ratus',
            500 => 'Lima Ratus',
            600 => 'Enam Ratus',
            700 => 'Tujuh Ratus',
            800 => 'Delapan Ratus',
            900 => 'Sembilan Ratus',
            1000 => 'Seribu'
        );

        if ($number < 21) {
            return $words[$number];
        } elseif ($number < 100) {
            if ($number % 10 == 0) {
                return $words[$number];
            } else {
                return $words[floor($number / 10) * 10] . ' ' . $words[$number % 10];
            }
        } elseif ($number < 1000) {
            if ($number % 100 == 0) {
                return $words[floor($number / 100) * 100];
            } else {
                return $words[floor($number / 100) * 100] . ' ' . $this->nomorKata($number % 100);
            }
        } elseif ($number < 1000000) {
            if ($number % 1000 == 0) {
                return $this->nomorKata(floor($number / 1000)) . ' Ribu';
            } else {
                return $this->nomorKata(floor($number / 1000)) . ' Ribu ' . $this->nomorKata($number % 1000);
            }
        }

        return $number;
    }

    public function tanggalDalamFormatBaru($date)
    {
        setlocale(LC_TIME, 'id_ID');
        return strftime('%d %B %Y', strtotime($date));
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            // User hanya bisa melihat BAP yang mereka buat
            $data = BAP::where('user_id', $user->id)->get();
        } else if ($user->role === 'admin' || $user->role === 'kabupaten') {
            // Admin dan kabupaten bisa melihat semua BAP
            $data = BAP::all();
        } else {
            $data = collect();
        }

        $jamaahCount = Jamaah::count();

        return view('travel.listBAP', compact('data', 'jamaahCount'));
    }


    public function simpan(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'ppiuname' => 'required|string|max:255',
            'address_phone' => 'required|string|max:255',
            'kab_kota' => 'required|string|max:255',
            'people' => 'required|integer',
            'package' => 'required|string|max:255',
            'price' => 'required|numeric',
            'datetime' => 'required|date',
            'airlines' => 'required|string|max:255',
            'returndate' => 'required|date',
            'airlines2' => 'required|string|max:255',
        ]);

        // Ambil semua data dari request dan tambahkan user_id
        $data = $request->all();
        $data['user_id'] = auth()->id();

        // Simpan data ke dalam database
        BAP::create($data);

        // Redirect ke halaman yang diinginkan setelah data disimpan
        return redirect()->route('bap')->with('success', 'Data berhasil disimpan.');
    }

    public function uploadPDF(Request $request, $id)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:500',
        ]);

        $data = BAP::findOrFail($id);

        if ($request->hasFile('pdf_file')) {
            $pdfFile = $request->file('pdf_file');
            $pdfFilePath = $pdfFile->store('uploads', 'public');
            $data->pdf_file_path = $pdfFilePath;
            $data->save();
        }

        return redirect()->route('detail.bap', ['id' => $id])->with('success', 'PDF berhasil diupload.');
    }

    public function ajukan($id)
    {
        $data = BAP::findOrFail($id);
        if ($data->status === 'pending') {
            $data->status = 'diajukan';
            $data->save();
        }

        return redirect()->route('detail.bap', ['id' => $id])->with('success', 'Berhasil mengajukan BAP');
    }

    public function updateStatus(Request $request, $id)
    {
        // Check if user has permission to update status
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'kabupaten'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah status BAP.');
        }

        $request->validate([
            'status' => 'required|in:pending,diajukan,diproses,diterima',
            'nomor_surat' => 'nullable|string|max:10',
            'bulan_surat' => 'nullable|string|max:2',
            'tahun_surat' => 'nullable|string|max:4',
        ]);

        $data = BAP::findOrFail($id);
        $oldStatus = $data->status;
        
        // Validasi: Jika status diubah ke 'diterima', pastikan status sebelumnya adalah 'diproses' dan nomor surat sudah ada
        if ($request->status === 'diterima') {
            if ($oldStatus !== 'diproses') {
                return redirect()->back()->with('error', 'Status harus diubah ke "diproses" terlebih dahulu sebelum bisa diubah menjadi "diterima".');
            }
            if (!$data->nomor_surat) {
                return redirect()->back()->with('error', 'Status tidak bisa diubah menjadi "diterima" karena nomor surat belum diisi. Silakan ubah ke status "diproses" terlebih dahulu.');
            }
        }
        
        $data->status = $request->status;
        
        // Jika status berubah menjadi 'diproses' dan data nomor diisi
        if ($request->status === 'diproses' && $request->filled('nomor_surat')) {
            // Generate nomor surat lengkap
            $nomor = str_pad($request->nomor_surat, 3, '0', STR_PAD_LEFT);
            $bulan = str_pad($request->bulan_surat ?? date('m'), 2, '0', STR_PAD_LEFT);
            $tahun = $request->tahun_surat ?? date('Y');
            
            $nomorSuratLengkap = "B-{$nomor}/Kw.18.04/2/Hj.00/{$bulan}/{$tahun}";
            $data->nomor_surat = $nomorSuratLengkap;
        }
        
        $data->save();

        $message = 'Status berhasil diubah dari ' . ucfirst($oldStatus) . ' menjadi ' . ucfirst($request->status);
        if ($request->status === 'diproses' && $request->filled('nomor_surat')) {
            $message .= ' dengan nomor surat: ' . $data->nomor_surat;
        }

        return redirect()->route('bap')->with('success', $message);
    }

    public function showKeberangkatan()
    {
        return view('travel.keberangkatan');
    }

    public function getEvents()
    {
        $events = BAP::all()->map(function ($event) {
            return [
                'title' => $event->ppiuname,
                'start' => $event->datetime,
                'color' => '#2563eb', // Bisa disesuaikan berdasarkan package
                'extendedProps' => [
                    'name' => $event->name,
                    'jabatan' => $event->jabatan,
                    'ppiuname' => $event->ppiuname,
                    'address_phone' => $event->address_phone,
                    'kab_kota' => $event->kab_kota,
                    'people' => $event->people,
                    'package' => $event->package,
                    'price' => $event->price,
                    'airlines' => $event->airlines,
                    'returndate' => $event->returndate,
                    'airlines2' => $event->airlines2
                ]
            ];
        });

        return response()->json($events);
    }

    public function verifyQRCode(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        try {
            $qrData = json_decode($request->qr_data, true);
            
            if (!$qrData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data QR Code tidak valid'
                ]);
            }

            // Verifikasi data
            $verificationResult = [
                'nomor_surat' => $qrData['nomor_surat'] ?? 'Tidak ditemukan',
                'nama_petugas' => $qrData['nama_petugas'] ?? 'Tidak ditemukan',
                'nip_petugas' => $qrData['nip_petugas'] ?? 'Tidak ditemukan',
                'jabatan_petugas' => $qrData['jabatan_petugas'] ?? 'Tidak ditemukan',
                'tanggal_tanda_tangan' => $qrData['tanggal_tanda_tangan'] ?? 'Tidak ditemukan',
                'status_dokumen' => $qrData['status_dokumen'] ?? 'Tidak ditemukan',
                'verifikasi_digital' => $qrData['verifikasi_digital'] ?? false,
                'hash_verifikasi' => $qrData['hash_verifikasi'] ?? 'Tidak ditemukan'
            ];

            // Cek apakah dokumen masih valid
            $bap = BAP::where('nomor_surat', $qrData['nomor_surat'])->first();
            
            if (!$bap) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan dalam database',
                    'data' => $verificationResult
                ]);
            }

            // Verifikasi hash
            $expectedHash = hash('sha256', $bap->id . $bap->nomor_surat . $bap->user_id);
            $hashValid = ($qrData['hash_verifikasi'] === $expectedHash);

            return response()->json([
                'success' => true,
                'message' => $hashValid ? 'Tanda tangan digital valid' : 'Tanda tangan digital tidak valid',
                'data' => $verificationResult,
                'hash_valid' => $hashValid,
                'dokumen_valid' => $bap->status === 'diterima'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saat verifikasi: ' . $e->getMessage()
            ]);
        }
    }

    public function showVerifyQR()
    {
        return view('travel.verifyQR');
    }
}
