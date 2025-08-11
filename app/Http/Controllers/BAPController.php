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
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }
        
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
                // Hanya ambil jamaah umrah dari travel company ini
                $jamaahCount = Jamaah::where('jenis_jamaah', 'umrah')
                                    ->where('travel_id', $user->travel_id)
                                    ->count();
            } else if ($travelData->Status === 'PIHK') {
                // Hanya ambil jamaah haji dari travel company ini
                $jamaahCount = Jamaah::where('jenis_jamaah', 'haji')
                                    ->where('travel_id', $user->travel_id)
                                    ->count();
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
                // Generate token untuk verifikasi manual
                $token = strtoupper(substr(hash('sha256', $data->id . $data->nomor_surat . $data->user_id . $data->ppiuname), 0, 8));
                
                // Generate QR Code dengan URL relative untuk verifikasi
                $verificationUrl = '/verify-e-sign?token=' . $token . '&bap_id=' . $data->id;
                
                $qrCode = \Endroid\QrCode\QrCode::create($verificationUrl)
                ->setSize(300)
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
                
                // Get the public URL for the QR code
                $qrCodeData = asset('storage/BAP_Sign/bap_qrcode_' . $data->id . '.png');
                
            } catch (\Exception $e) {
                \Log::error('QR Code generation failed:', ['error' => $e->getMessage()]);
                $qrCodeData = null;
            }
        } else {
            $qrCodeData = null;
        }
        
        return view('travel.printBAP', compact('data', 'qrCodeData', 'formattedDate', 'formattedReturnDate', 'token'));
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
        $timestamp = strtotime($date);
        $hari = date('l', $timestamp);
        $tanggal = date('j', $timestamp);
        $bulan = date('F', $timestamp);
        $tahun = date('Y', $timestamp);
        
        // Konversi hari ke bahasa Indonesia
        $hariIndonesia = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        
        // Konversi bulan ke bahasa Indonesia
        $bulanIndonesia = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];
        
        $hariText = $hariIndonesia[$hari] ?? $hari;
        $bulanText = $bulanIndonesia[$bulan] ?? $bulan;
        $tanggalText = $this->nomorKata($tanggal);
        $tahunText = $this->nomorKata($tahun);
        
        return "Pada hari ini {$hariText}, tanggal {$tanggalText}, bulan {$bulanText}, tahun {$tahunText}";
    }

    public function index()
    {
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        if ($user->role === 'user') {
            // User (travel) hanya bisa melihat BAP yang mereka buat dan sesuai kabupatennya
            $data = BAP::where('user_id', $user->id)
                       ->where('kab_kota', $user->kabupaten)
                       ->get();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten hanya bisa melihat BAP dari kabupatennya
            $data = BAP::where('kab_kota', $user->kabupaten)->get();
        } else if ($user->role === 'admin') {
            // Admin bisa melihat semua BAP
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

        return redirect()->route('bap')->with('success', 'PDF berhasil diupload.');
    }

    public function ajukan($id)
    {
        $data = BAP::findOrFail($id);
        if ($data->status === 'pending') {
            $data->status = 'diajukan';
            $data->save();
        }

        return redirect()->route('bap')->with('success', 'Berhasil mengajukan BAP');
    }

    public function updateStatus(Request $request, $id)
    {
        // Check if user has permission to update status
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }
        
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
            'qr_data' => 'nullable|string',
            'token' => 'nullable|string'
        ]);

        try {
            // Jika ada QR data, proses QR Code
            if ($request->filled('qr_data')) {
                $qrData = json_decode($request->qr_data, true);
                
                if (!$qrData) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data QR Code tidak valid'
                    ]);
                }

                // Cek apakah ini QR Code BAP
                if (isset($qrData['type']) && $qrData['type'] === 'bap_verification') {
                    return $this->verifyBAPData($qrData);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jenis QR Code tidak dikenali'
                    ]);
                }
            }
            
            // Jika ada token, proses token
            if ($request->filled('token')) {
                return $this->verifyBAPByToken($request->token);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data QR Code atau Token diperlukan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saat verifikasi: ' . $e->getMessage()
            ]);
        }
    }

    private function verifyBAPData($qrData)
    {
                        // Verifikasi data BAP
                $verificationResult = [
                    'jenis_dokumen' => 'Berita Acara Pelaporan (BAP)',
                    'nomor_surat' => $qrData['nomor_surat'] ?? 'Tidak ditemukan',
                    'nama_travel' => $qrData['nama_travel'] ?? 'Tidak ditemukan',
                    'nama_petugas' => $qrData['nama_petugas'] ?? 'Bidang PHU Kanwil NTB',
                    'jabatan_petugas' => $qrData['jabatan_petugas'] ?? 'Verifikator',
                    'tanggal_dibuat' => $qrData['tanggal_dibuat'] ?? 'Tidak ditemukan',
                    'status_dokumen' => $qrData['status_dokumen'] ?? 'Tidak ditemukan',
                    'token' => $qrData['token'] ?? 'Tidak ditemukan'
                ];

        // Cek apakah dokumen masih valid di database
        $bap = BAP::where('id', $qrData['bap_id'])->where('nomor_surat', $qrData['nomor_surat'])->first();
        
        if (!$bap) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen BAP tidak ditemukan dalam database',
                'data' => $verificationResult
            ]);
        }

        // Verifikasi hash dengan data yang sama seperti saat generate
        $expectedHash = hash('sha256', $bap->id . $bap->nomor_surat . $bap->user_id . $bap->ppiuname);
        $hashValid = ($qrData['hash_verifikasi'] === $expectedHash);

        return response()->json([
            'success' => true,
            'message' => $hashValid ? 'Tanda tangan digital BAP valid' : 'Tanda tangan digital BAP tidak valid',
            'data' => $verificationResult,
            'hash_valid' => $hashValid,
            'dokumen_valid' => $bap->status === 'diterima',
            'dokumen_aktif' => $bap->status !== 'pending'
        ]);
    }

    private function verifyBAPByToken($token)
    {
        // Cari BAP berdasarkan token
        $baps = BAP::where('status', 'diterima')->get();
        $foundBap = null;
        
        foreach ($baps as $bap) {
            $expectedToken = strtoupper(substr(hash('sha256', $bap->id . $bap->nomor_surat . $bap->user_id . $bap->ppiuname), 0, 8));
            if ($token === $expectedToken) {
                $foundBap = $bap;
                break;
            }
        }

        if (!$foundBap) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau dokumen tidak ditemukan'
            ]);
        }

                        // Verifikasi data BAP
                $verificationResult = [
                    'jenis_dokumen' => 'Berita Acara Pelaporan (BAP)',
                    'nomor_surat' => $foundBap->nomor_surat,
                    'nama_travel' => $foundBap->ppiuname,
                    'nama_petugas' => 'Bidang PHU Kanwil NTB',
                    'jabatan_petugas' => 'Verifikator',
                    'tanggal_dibuat' => $foundBap->created_at->format('Y-m-d H:i:s'),
                    'status_dokumen' => $foundBap->status,
                    'token' => $token
                ];

        return response()->json([
            'success' => true,
            'message' => 'Token valid - Dokumen BAP ditemukan',
            'data' => $verificationResult,
            'hash_valid' => true,
            'dokumen_valid' => $foundBap->status === 'diterima',
            'dokumen_aktif' => $foundBap->status !== 'pending'
        ]);
    }

    public function showVerifyQR(Request $request)
    {
        $token = $request->get('token');
        $bapId = $request->get('bap_id');
        
        // Jika ada token dan bap_id, langsung verifikasi
        if ($token && $bapId) {
            $result = $this->verifyBAPByToken($token);
            $verificationData = json_decode($result->getContent(), true);
            
            return view('travel.verifyESign', compact('verificationData', 'token', 'bapId'));
        }
        
        return view('travel.verifyESign');
    }
}
