<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BAP;
use App\Models\Jamaah;
use Illuminate\Http\Request;
use App\Models\TravelCompany;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\BapJamaahService;
use App\Services\BapVerificationService;
use Illuminate\Support\Facades\DB;

class BAPController extends Controller
{
    public function __construct(
        private BapJamaahService $bapJamaahService,
        private BapVerificationService $bapVerificationService,
    ) {}
    public function showFormBAP()
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $context = $this->resolveBapFormContext();

        if ($context instanceof \Illuminate\Http\RedirectResponse) {
            return $context;
        }

        return view('travel.pengajuanBAP', $context);
    }

    public function editFormBAP($id)
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $bap = $this->findWizardBap($id)->load('jamaah');
        $context = $this->resolveBapFormContext($bap);

        if ($context instanceof \Illuminate\Http\RedirectResponse) {
            return $context;
        }

        return view('travel.pengajuanBAP', $context);
    }

    public function downloadSuratPernyataanTemplate()
    {
        $user = auth()->user();
        $travel = $user?->travel_id ? TravelCompany::find($user->travel_id) : null;

        return Pdf::loadView('travel.pdf.template-surat-pernyataan', [
            'pimpinan' => $travel?->Pimpinan ?? '________________',
            'penyelenggara' => $travel?->Penyelenggara ?? '________________',
            'kabKota' => $travel?->kab_kota ?? '________________',
        ])->download('template-surat-pernyataan-bap.pdf');
    }

    public function jamaahPickerOptions(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        $travel = $user->travel_id ? TravelCompany::find($user->travel_id) : null;
        $ignoreBapId = $request->filled('ignore_bap_id') ? (int) $request->ignore_bap_id : null;
        $ppiuname = $request->string('ppiuname')->toString();

        if ($request->boolean('available_only')) {
            return response()->json([
                'ids' => $this->bapJamaahService->availableIdsForPicker($user, $travel, [
                    'ppiuname' => $ppiuname,
                    'ignore_bap_id' => $ignoreBapId,
                ]),
            ]);
        }

        $paginator = $this->bapJamaahService->paginateForPicker($user, $travel, [
            'search' => $request->string('search')->toString(),
            'ppiuname' => $ppiuname,
            'per_page' => min(50, max(10, (int) $request->input('per_page', 15))),
            'ignore_bap_id' => $ignoreBapId,
        ]);

        $busyIds = array_flip($this->bapJamaahService->busyJamaahIds($ignoreBapId));

        return response()->json([
            'data' => collect($paginator->items())->map(fn (Jamaah $jamaah) => [
                'id' => $jamaah->id,
                'nama' => $jamaah->nama,
                'nik' => $jamaah->nik,
                'ppiuname' => $jamaah->travel?->Penyelenggara,
                'is_busy' => isset($busyIds[$jamaah->id]),
            ])->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function detail($id)
    {
        $data = BAP::findOrFail($id);
        $user = auth()->user();

        if ($user?->role === 'user' && $data->user_id === $user->id && $data->status === 'pending') {
            $wizardRoute = \App\Support\BapWizardStatus::wizardRouteName($data);

            if ($wizardRoute) {
                return redirect()->route($wizardRoute, $data->id);
            }
        }

        return view('travel.detailBAP', ['data' => $data->load('jamaah')]);
    }

    public function showWizardUpload($id)
    {
        $data = $this->findWizardBap($id)->load('jamaah');

        return view('travel.bap-wizard-upload', compact('data'));
    }

    public function showWizardReview($id)
    {
        $data = $this->findWizardBap($id)->load('jamaah');

        if (! $data->pdf_file_path) {
            return redirect()
                ->route('bap.wizard.upload', $data->id)
                ->with('error', 'Unggah surat pernyataan PDF terlebih dahulu.');
        }

        return view('travel.bap-wizard-review', compact('data'));
    }

    private function findWizardBap($id): BAP
    {
        $data = BAP::findOrFail($id);
        $this->authorizeWizardBap($data);

        return $data;
    }

    private function authorizeWizardBap(BAP $bap): void
    {
        $this->authorizeBapAccess($bap);

        if ($bap->status !== 'pending') {
            abort(403, 'Pengajuan ini sudah tidak dapat diubah.');
        }
    }

    private function authorizeBapAccess(BAP $bap): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Anda harus login terlebih dahulu.');
        }

        if (in_array($user->role, ['admin', 'kabupaten'], true)) {
            return;
        }

        if ($user->role === 'user' && $bap->user_id === $user->id) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
    }

    public function printBAP($id)
    {
        $data = BAP::findOrFail($id);

        $formattedDate = \Carbon\Carbon::parse($data->datetime)->translatedFormat('d F Y');
        $formattedReturnDate = \Carbon\Carbon::parse($data->returndate)->translatedFormat('d F Y');

        $data = $this->bapVerificationService->ensureTravelToken($data);

        $travelQrCodeData = $this->bapVerificationService->qrDataUri(
            $this->bapVerificationService->verificationUrl($data->travel_token, $data->id)
        );

        $kanwilQrCodeData = null;
        $token = null;

        if ($data->status === 'diterima') {
            $data = $this->bapVerificationService->ensureKanwilToken($data);
            $token = $this->bapVerificationService->combinedToken($data);

            if ($token) {
                $kanwilQrCodeData = $this->bapVerificationService->qrDataUri(
                    $this->bapVerificationService->verificationUrl($token, $data->id)
                );
            }
        }

        return view('travel.printBAP', compact(
            'data',
            'travelQrCodeData',
            'kanwilQrCodeData',
            'formattedDate',
            'formattedReturnDate',
            'token'
        ));
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
            $data = BAP::query()
                ->where('user_id', $user->id)
                ->where('kab_kota', $user->kabupaten)
                ->latest()
                ->paginate(15)
                ->withQueryString();
        } elseif ($user->role === 'kabupaten') {
            $data = BAP::query()
                ->where('kab_kota', $user->kabupaten)
                ->latest()
                ->paginate(15)
                ->withQueryString();
        } elseif ($user->role === 'admin') {
            $data = BAP::query()
                ->latest()
                ->paginate(15)
                ->withQueryString();
        } else {
            $data = BAP::query()
                ->whereRaw('1 = 0')
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        $travel = $user->travel_id ? TravelCompany::find($user->travel_id) : null;
        $jamaahCount = $this->bapJamaahService->countForForm($user, $travel);

        return view('travel.listBAP', compact('data', 'jamaahCount'));
    }


    public function simpan(Request $request)
    {
        $user = auth()->user();
        $payload = $this->validatedBapPayload($request, $user);

        $data = $payload['data'];
        $data['user_id'] = $user->id;

        $bap = BAP::create($data);
        $bap->jamaah()->sync($payload['jamaah_ids']);
        $this->bapVerificationService->ensureTravelToken($bap);

        return redirect()
            ->route('bap.wizard.upload', $bap->id)
            ->with('success', 'Data keberangkatan tersimpan. Lanjutkan dengan mengunggah surat pernyataan PDF.');
    }

    public function update(Request $request, $id)
    {
        $bap = $this->findWizardBap($id);
        $user = auth()->user();
        $payload = $this->validatedBapPayload($request, $user, $bap->id);

        $bap->update($payload['data']);
        $bap->jamaah()->sync($payload['jamaah_ids']);

        $nextRoute = $bap->fresh()->pdf_file_path ? 'bap.wizard.review' : 'bap.wizard.upload';

        return redirect()
            ->route($nextRoute, $bap->id)
            ->with('success', 'Draft berhasil diperbarui.');
    }

    /**
     * @return array{data: array<string, mixed>, jamaah_ids: list<int>}|void
     */
    private function validatedBapPayload(Request $request, $user, ?int $ignoreBapId = null): array
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'ppiuname' => 'required|string|max:255',
            'address_phone' => 'required|string|max:255',
            'kab_kota' => 'required|string|max:255',
            'jamaah_ids' => 'required|array|min:1',
            'jamaah_ids.*' => 'integer',
            'days' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'datetime' => 'required|date',
            'airlines' => 'required|string|max:255',
            'returndate' => 'required|date',
            'airlines2' => 'required|string|max:255',
        ]);

        $selected = $this->bapJamaahService->validateSelection(
            $request->input('jamaah_ids', []),
            $user,
            $request->ppiuname,
            $ignoreBapId
        );

        $data = $request->except(['package', 'price_display', 'jamaah_ids', 'people', '_method', '_token']);
        $data['people'] = $selected->count();

        return [
            'data' => $data,
            'jamaah_ids' => $selected->pluck('id')->all(),
        ];
    }

    /**
     * @return array<string, mixed>|\Illuminate\Http\RedirectResponse
     */
    private function resolveBapFormContext(?BAP $bap = null)
    {
        $user = auth()->user();
        $jamaahCount = 0;
        $travelData = null;

        if ($user->role === 'admin') {
            $jamaahCount = Jamaah::count();
            if ($jamaahCount === 0) {
                return redirect()->back()->with('error', 'Tidak bisa menambahkan, karena belum ada data jamaah.');
            }
        } elseif ($user->role !== 'kabupaten') {
            $travelData = TravelCompany::find($user->travel_id);

            if (! $travelData) {
                return redirect()->back()->with('error', 'Data travel tidak ditemukan.');
            }

            $jenis = $travelData->Status === 'PIHK' ? 'haji' : 'umrah';
            $jamaahCount = Jamaah::where('jenis_jamaah', $jenis)
                ->where('travel_id', $user->travel_id)
                ->count();

            if ($jamaahCount === 0) {
                return redirect()->back()->with('error', 'Tidak bisa menambahkan, karena belum ada data jamaah.');
            }
        }

        $ppiuList = TravelCompany::select('penyelenggara')->distinct()->get();
        $jamaahTotalCount = $this->bapJamaahService->countForForm($user, $travelData);

        if ($jamaahTotalCount === 0) {
            return redirect()->back()->with('error', 'Tidak bisa menambahkan, karena belum ada data jamaah.');
        }

        $selectedJamaah = collect();
        if ($requestIds = old('jamaah_ids')) {
            $selectedJamaah = Jamaah::query()
                ->whereIn('id', $requestIds)
                ->get(['id', 'nama', 'nik']);
        } elseif ($bap) {
            $selectedJamaah = $bap->jamaah()->get(['jamaah.id', 'nama', 'nik']);
        }

        return compact('ppiuList', 'jamaahCount', 'travelData', 'jamaahTotalCount', 'selectedJamaah', 'bap');
    }

    public function uploadPDF(Request $request, $id)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:500',
        ]);

        $data = BAP::findOrFail($id);
        $this->authorizeBapAccess($data);

        if ($request->hasFile('pdf_file')) {
            $pdfFile = $request->file('pdf_file');
            $pdfFilePath = $pdfFile->store('uploads', 'public');
            $data->pdf_file_path = $pdfFilePath;
            $data->save();
        }

        $fromWizard = $request->boolean('wizard');

        return redirect()
            ->route($fromWizard ? 'bap.wizard.review' : 'bap', $fromWizard ? $id : [])
            ->with('success', $fromWizard
                ? 'PDF berhasil diunggah. Periksa kembali data sebelum mengajukan.'
                : 'PDF berhasil diupload.');
    }

    public function ajukan(Request $request, $id)
    {
        $data = BAP::findOrFail($id);
        $this->authorizeWizardBap($data);

        if (! $data->pdf_file_path) {
            return redirect()
                ->route('bap.wizard.upload', $data->id)
                ->with('error', 'Unggah surat pernyataan PDF terlebih dahulu.');
        }

        if ($data->status === 'pending') {
            $data->status = 'diajukan';
            $data->save();
        }

        return redirect()
            ->route('bap')
            ->with('success', 'Pengajuan BA Pemberangkatan berhasil dikirim. Pantau status persetujuan di daftar.');
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
        ]);

        $data = BAP::findOrFail($id);
        $oldStatus = $data->status;
        
        $data->status = $request->status;
        
        // Jika status berubah menjadi 'diterima', generate nomor surat otomatis
        if ($request->status === 'diterima') {
            // Cari nomor urut terakhir untuk bulan dan tahun saat ini
            $currentMonth = date('m');
            $currentYear = date('Y');
            
            $lastBAP = BAP::where('status', 'diterima')
                         ->where('nomor_surat', 'like', "%/{$currentMonth}/{$currentYear}")
                         ->orderBy('id', 'desc')
                         ->first();
            
            $nextNumber = 1;
            if ($lastBAP && $lastBAP->nomor_surat) {
                // Extract nomor dari format B-{nomor}/Kw.18.04/2/Hj.00/{bulan}/{tahun}
                if (preg_match('/B-(\d+)\/Kw\.18\.04\/2\/Hj\.00\/\d{2}\/\d{4}/', $lastBAP->nomor_surat, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                }
            }
            
            // Generate nomor surat lengkap
            $bulan = str_pad($currentMonth, 2, '0', STR_PAD_LEFT);
            $tahun = $currentYear;
            
            $nomorSuratLengkap = "B-{$nextNumber}/Kw.18.04/2/Hj.00/{$bulan}/{$tahun}";
            $data->nomor_surat = $nomorSuratLengkap;
        }

        $data->save();

        if ($request->status === 'diterima') {
            $this->bapVerificationService->ensureKanwilToken($data->fresh());
        }

        $message = 'Status berhasil diubah dari ' . ucfirst($oldStatus) . ' menjadi ' . ucfirst($request->status);
        if ($request->status === 'diterima') {
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
        $query = BAP::where('status', 'diterima');
        $query = $this->scopeKeberangkatanQuery($query);

        $events = $query->get()->map(function ($event) {
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
                    'days' => $event->days,
                    'price' => $event->price,
                    'airlines' => $event->airlines,
                    'returndate' => $event->returndate,
                    'airlines2' => $event->airlines2
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<BAP>  $query
     * @return \Illuminate\Database\Eloquent\Builder<BAP>
     */
    private function scopeKeberangkatanQuery($query)
    {
        $user = auth()->user();

        if (! $user) {
            return $query;
        }

        if ($user->role === 'user') {
            $penyelenggara = $user->travel?->Penyelenggara ?? $user->cabang?->Penyelenggara;

            if ($penyelenggara) {
                return $query->where('ppiuname', $penyelenggara);
            }

            return $query->where('user_id', $user->id)
                ->where('kab_kota', $user->kabupaten);
        }

        if ($user->role === 'kabupaten') {
            return $query->where('kab_kota', $user->kabupaten);
        }

        return $query;
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
                    'jenis_dokumen' => 'BA Pemberangkatan',
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
                'message' => 'Dokumen BA Pemberangkatan tidak ditemukan dalam database',
                'data' => $verificationResult
            ]);
        }

        // Verifikasi hash dengan data yang sama seperti saat generate
        $expectedHash = hash('sha256', $bap->id . $bap->nomor_surat . $bap->user_id . $bap->ppiuname);
        $hashValid = ($qrData['hash_verifikasi'] === $expectedHash);

        return response()->json([
            'success' => true,
            'message' => $hashValid ? 'Tanda tangan digital BA Pemberangkatan valid' : 'Tanda tangan digital BA Pemberangkatan tidak valid',
            'data' => $verificationResult,
            'hash_valid' => $hashValid,
            'dokumen_valid' => $bap->status === 'diterima',
            'dokumen_aktif' => $bap->status !== 'pending'
        ]);
    }

    private function verifyBAPByToken(string $token, ?int $bapId = null)
    {
        $foundBap = null;
        $verificationLevel = null;

        if ($bapId) {
            $bap = BAP::query()->where('id', $bapId)->first();

            if ($bap && $this->bapVerificationService->matchesToken($bap, $token)) {
                $foundBap = $bap;
                $verificationLevel = $this->combinedTokenMatches($bap, $token) ? 'lengkap' : 'travel';
            }
        }

        if (! $foundBap) {
            $travelTokenQuery = BAP::query()->where('travel_token', $token);

            if ($bapId) {
                $travelTokenQuery->where('id', $bapId);
            }

            $foundBap = $travelTokenQuery->first();

            if ($foundBap) {
                $verificationLevel = 'travel';
            }
        }

        if (! $foundBap) {
            $tokenQuery = BAP::query()
                ->whereNotNull('travel_token')
                ->whereNotNull('kanwil_token');

            if ($bapId) {
                $tokenQuery->where('id', $bapId);
            }

            $combinedExpression = DB::getDriverName() === 'sqlite'
                ? '(travel_token || kanwil_token)'
                : 'CONCAT(travel_token, kanwil_token)';

            $foundBap = $tokenQuery
                ->whereRaw("{$combinedExpression} = ?", [$token])
                ->first();

            if ($foundBap) {
                $verificationLevel = 'lengkap';
            }
        }

        if (! $foundBap) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau dokumen tidak ditemukan',
            ]);
        }

        $verificationResult = [
            'jenis_dokumen' => 'BA Pemberangkatan',
            'nomor_surat' => $foundBap->nomor_surat,
            'nama_travel' => $foundBap->ppiuname,
            'nama_petugas' => 'Bidang PHU Kanwil NTB',
            'jabatan_petugas' => 'Verifikator',
            'tanggal_dibuat' => $foundBap->created_at->format('Y-m-d H:i:s'),
            'status_dokumen' => $foundBap->status,
            'token' => $token,
            'tingkat_verifikasi' => $verificationLevel === 'lengkap' ? 'Travel + Kanwil' : 'Travel',
        ];

        return response()->json([
            'success' => true,
            'message' => $verificationLevel === 'lengkap'
                ? 'Token valid. Dokumen BA Pemberangkatan telah diverifikasi Travel dan Kanwil.'
                : 'Token valid. Tanda tangan Travel terverifikasi. Menunggu persetujuan Kanwil.',
            'data' => $verificationResult,
            'hash_valid' => true,
            'dokumen_valid' => $foundBap->status === 'diterima' && $verificationLevel === 'lengkap',
            'dokumen_aktif' => $foundBap->status !== 'pending',
        ]);
    }

    private function combinedTokenMatches(BAP $bap, string $token): bool
    {
        $combined = $this->bapVerificationService->combinedToken($bap);

        return $combined !== null && hash_equals($combined, $token);
    }

    public function showVerifyQR(Request $request)
    {
        $token = $request->get('token');
        $bapId = $request->get('bap_id');
        
        // Jika ada token dan bap_id, langsung verifikasi
        if ($token && $bapId) {
            $result = $this->verifyBAPByToken($token, (int) $bapId);
            $verificationData = json_decode($result->getContent(), true);
            
            return view('travel.verifyESign', compact('verificationData', 'token', 'bapId'));
        }
        
        return view('travel.verifyESign');
    }

    public function showVerifyQRPublic(Request $request)
    {
        $token = $request->get('token');
        $bapId = $request->get('bap_id');
        
        // Jika ada token dan bap_id, langsung verifikasi
        if ($token && $bapId) {
            $result = $this->verifyBAPByToken($token, (int) $bapId);
            $verificationData = json_decode($result->getContent(), true);
            
            return view('travel.verifyESignPublic', compact('verificationData', 'token', 'bapId'));
        }
        
        return view('travel.verifyESignPublic');
    }
}
