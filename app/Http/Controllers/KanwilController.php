<?php

namespace App\Http\Controllers;

use App\Helpers\ExceptionMessageHelper;
use App\Helpers\StorageHelper;
use App\Helpers\ValidationHelper;
use App\Models\CabangTravel;
use App\Enums\TravelRegistrationStatus;
use App\Notifications\V2\CabangRecommendedNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TravelCompany;
use App\Imports\CabangTravelImport;
use App\Exports\TravelPusatExport;
use App\Exports\TravelCabangExport;
use Illuminate\Support\Facades\Storage;
use App\Support\NtbKabupatenMap;
use App\Support\KabupatenResourceGuard;
use App\Support\KabupatenScopeFilter;
use Maatwebsite\Excel\Facades\Excel;


class KanwilController extends Controller
{
    // Di dalam KanwilController

    public function showFormTravel()
    {
        KabupatenResourceGuard::requireAdmin(auth()->user());

        return view('kanwil.formTravel', [
            'kabupatens' => NtbKabupatenMap::names(),
        ]);
    }

    public function store(Request $request)
    {
        KabupatenResourceGuard::requireAdmin(auth()->user());
        $validatedData = ValidationHelper::validate($request, ValidationHelper::travelCompanyDataRules());

        $validatedData['registration_status'] = TravelRegistrationStatus::Approved;
        $validatedData['verified_at'] = now();
        $validatedData['verified_by'] = auth()->id();

        $travelCompany = TravelCompany::create($validatedData);

        $travelCompany->setDefaultCapabilities();
        $travelCompany->description = $travelCompany->getTravelTypeDescription();
        $travelCompany->save();

        return redirect()->route('travel')->with('success', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        $travelCompany = TravelCompany::with('user')->findOrFail($id);
        KabupatenResourceGuard::authorizeTravelAsStaff(auth()->user(), $travelCompany);

        return view('kanwil.editTravel', [
            'travelCompany' => $travelCompany,
            'kabupatens' => NtbKabupatenMap::names(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $travelCompany = TravelCompany::with('user')->findOrFail($id);
        KabupatenResourceGuard::authorizeTravelAsStaff(auth()->user(), $travelCompany);

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
            KabupatenResourceGuard::authorizeTravelAsStaff(auth()->user(), $travelCompany);
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
                'message' => 'Data tidak valid. Periksa kembali isian Anda.',
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Travel company not found', ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Travel company tidak ditemukan'
            ], 404);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // Penolakan akses bukan kegagalan aplikasi. Tanpa ini, abort() dari
            // penjaga ikut tertangkap catch di bawah dan dibalas 500, sehingga
            // percobaan akses yang ditolak tercatat seolah sistemnya rusak.
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error in updateStatus', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => ExceptionMessageHelper::forUser($e, ExceptionMessageHelper::GENERIC_SAVE),
            ], 500);
        }
    }

    public function showTravel(Request $request)
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all');

        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $data = $this->buildTravelListingQuery($request, $user)
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tableBody' => view('kanwil.partials.travel-table-body', compact('data'))->render(),
                'pagination' => view('kanwil.partials.travel-pagination', compact('data'))->render(),
                'pagination_info' => [
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                    'total' => $data->total(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                ],
                'filters' => [
                    'search' => $request->get('search'),
                    'filter' => $filter,
                ],
            ]);
        }

        $pendingQuery = TravelCompany::pendingRegistration();
        if ($user && $user->role === 'kabupaten') {
            $filters = KabupatenScopeFilter::filtersForUser($user);
            KabupatenScopeFilter::applyOnColumn($pendingQuery, $filters, 'kab_kota');
        }
        $pendingCount = $pendingQuery->count();

        return view('kanwil.travel', [
            'data' => $data,
            'filter' => $filter,
            'pendingCount' => $pendingCount,
        ]);
    }

    private function buildTravelListingQuery(Request $request, $user)
    {
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
            $filters = KabupatenScopeFilter::filtersForUser($user);
            KabupatenScopeFilter::applyOnColumn($query, $filters, 'kab_kota');
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

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('Penyelenggara', 'like', "%{$search}%")
                    ->orWhere('Pimpinan', 'like', "%{$search}%")
                    ->orWhere('kab_kota', 'like', "%{$search}%")
                    ->orWhere('Telepon', 'like', "%{$search}%")
                    ->orWhere('Pusat', 'like', "%{$search}%");
            });
        }

        return $query;
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
        // Pendaftaran ditolak harus mendaftar ulang dari awal, jadi berkas
        // lamanya tidak akan dipakai lagi dan tidak perlu menumpuk di storage.
        $travel->deleteRegistrationDocuments();

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
        return view('kanwil.formCabangTravel', $this->cabangFormData());
    }

    public function storeCabangTravel(Request $request)
    {
        $user = auth()->user();

        $validatedData = ValidationHelper::validate($request, self::cabangDataRules());

        if ($user->role === 'kabupaten') {
            $validatedData['kabupaten'] = NtbKabupatenMap::normalize($user->kabupaten);
        }

        // Data yang diinput petugas dianggap sudah terverifikasi; alur peninjauan
        // hanya berlaku untuk cabang yang mendaftar mandiri.
        $validatedData['registration_status'] = TravelRegistrationStatus::Approved;
        $validatedData['verified_at'] = now();
        $validatedData['verified_by'] = $user->id;

        CabangTravel::create($validatedData);

        return redirect()->route('cabang.travel')->with('success', 'Data cabang travel berhasil disimpan.');
    }

    /** @return array<string, mixed> */
    private function cabangFormData(): array
    {
        $user = auth()->user();
        $travelsQuery = TravelCompany::approved()
            ->select('id', 'Penyelenggara', 'Pusat', 'Pimpinan', 'alamat_kantor_lama', 'alamat_kantor_baru', 'kab_kota')
            ->orderBy('Penyelenggara');

        if ($user->role === 'kabupaten') {
            $filters = KabupatenScopeFilter::filtersForUser($user);
            KabupatenScopeFilter::applyOnColumn($travelsQuery, $filters, 'kab_kota');
        }

        return [
            'travels' => $travelsQuery->get(),
            'kabupatens' => NtbKabupatenMap::names(),
        ];
    }

    /** @return array<string, mixed> */
    private static function cabangDataRules(): array
    {
        return [
            'travel_id' => ['nullable', 'integer', 'exists:travels,id'],
            'Penyelenggara' => 'required|string|max:255',
            // Kabupaten cabang menentukan Kabko mana yang berhak meninjau,
            // jadi harus salah satu wilayah NTB, bukan teks bebas.
            'kabupaten' => ['required', 'string', Rule::in(NtbKabupatenMap::names())],
            'pusat' => 'nullable|string|max:255',
            'pimpinan_pusat' => 'required|string|max:255',
            'alamat_pusat' => ValidationHelper::textRule(),
            'SK_BA' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'pimpinan_cabang' => 'required|string|max:255',
            'alamat_cabang' => ValidationHelper::textRule(),
            'telepon' => ValidationHelper::teleponRules(),
        ];
    }

    public function showCabangTravel(Request $request)
    {
        $user = auth()->user();

        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $data = $this->buildCabangTravelListingQuery($request, $user)
            ->orderByDesc('id_cabang')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tableBody' => view('kanwil.partials.cabang-travel-table-body', compact('data'))->render(),
                'pagination' => view('kanwil.partials.cabang-travel-pagination', compact('data'))->render(),
                'pagination_info' => [
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                    'total' => $data->total(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                ],
                'filters' => [
                    'search' => $request->get('search'),
                    'filter' => $request->get('filter'),
                ],
            ]);
        }

        return view('kanwil.cabangTravel', [
            'data' => $data,
            'antrian' => $this->cabangAntrianCounts($user),
        ]);
    }

    /**
     * Jumlah cabang yang masih menunggu tindakan, dipakai sebagai angka pada
     * tab penyaring. Tanpa ini petugas tidak tahu ada antrean tanpa mengklik
     * tabnya satu per satu.
     *
     * @return array{pending: int, menunggu_kanwil: int}
     */
    private function cabangAntrianCounts($user): array
    {
        $hitung = function (string $status) use ($user): int {
            $query = CabangTravel::query()->where('registration_status', $status);

            if ($user && $user->role === 'kabupaten') {
                KabupatenScopeFilter::applyOnColumn(
                    $query,
                    KabupatenScopeFilter::filtersForUser($user),
                    'kabupaten'
                );
            } elseif (! $user || $user->role !== 'admin') {
                return 0;
            }

            return $query->count();
        };

        return [
            'pending' => $hitung(TravelRegistrationStatus::Pending->value),
            'menunggu_kanwil' => $hitung(TravelRegistrationStatus::MenungguKanwil->value),
        ];
    }

    /**
     * Kabupaten/Kota mengunggah rekomendasi (BA laporan peninjauan) lalu
     * meneruskan cabang tersebut ke Kanwil untuk keputusan akhir.
     */
    public function recommendCabang(Request $request, $id_cabang)
    {
        $user = auth()->user();
        $cabang = CabangTravel::findOrFail($id_cabang);
        KabupatenResourceGuard::authorizeCabang($user, $cabang);

        if (! $cabang->isRegistrationPending()) {
            return back()->with('error', 'Cabang ini sudah diproses sebelumnya.');
        }

        $fileMaxKb = ValidationHelper::fileMaxKb(1.5);

        ValidationHelper::validate($request, [
            'dokumen_rekomendasi' => "required|file|mimes:pdf,jpg,jpeg,png|max:{$fileMaxKb}",
            'catatan_rekomendasi' => 'nullable|string|max:1000',
        ], ValidationHelper::fileMaxMb('dokumen_rekomendasi', 1.5));

        $cabang->update([
            'dokumen_rekomendasi' => StorageHelper::normalizePath(
                $request->file('dokumen_rekomendasi')->store('registrasi-cabang/rekomendasi', 'public')
            ),
            'catatan_rekomendasi' => $request->input('catatan_rekomendasi'),
            'registration_status' => TravelRegistrationStatus::MenungguKanwil,
            'recommended_at' => now(),
            'recommended_by' => $user->id,
        ]);

        // Yang perlu tahu hanya Kanwil. Kabko tidak perlu diberi tahu soal
        // rekomendasi yang baru saja dia kirim sendiri.
        app(NotificationService::class)->notifyAdmins(
            new CabangRecommendedNotification($cabang)
        );

        return redirect()
            ->route('cabang.travel', ['filter' => 'menunggu_kanwil'])
            ->with('success', "Rekomendasi {$cabang->Penyelenggara} terkirim. Menunggu keputusan Kanwil.");
    }

    /** Keputusan akhir ada di Kanwil, baik lewat rekomendasi Kabko maupun langsung. */
    public function approveCabang($id_cabang)
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $cabang = CabangTravel::findOrFail($id_cabang);

        if (! $cabang->isRegistrationOpen()) {
            return back()->with('error', 'Cabang ini sudah diproses sebelumnya.');
        }

        // Sejalan dengan penjagaan pada pendaftaran pusat: jangan menyetujui
        // berkas yang tercatat ada tetapi filenya sudah hilang dari storage.
        if ($hilang = $cabang->missingRegistrationDocuments()) {
            return back()->with(
                'error',
                'Berkas berikut tidak ditemukan di penyimpanan: ' . implode(', ', $hilang)
                . '. Minta pendaftar mengunggah ulang sebelum disetujui.'
            );
        }

        $cabang->update([
            'registration_status' => TravelRegistrationStatus::Approved,
            'registration_notes' => null,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()
            ->route('cabang.travel', ['filter' => 'approved'])
            ->with('success', "Pendaftaran cabang {$cabang->Penyelenggara} selesai dan disetujui.");
    }

    public function rejectCabang(Request $request, $id_cabang)
    {
        $user = auth()->user();
        $cabang = CabangTravel::findOrFail($id_cabang);
        KabupatenResourceGuard::authorizeCabang($user, $cabang);

        if (! $cabang->isRegistrationOpen()) {
            return back()->with('error', 'Cabang ini sudah diproses sebelumnya.');
        }

        ValidationHelper::validate($request, [
            'registration_notes' => 'required|string|max:1000',
        ]);

        $cabang->update([
            'registration_status' => TravelRegistrationStatus::Rejected,
            'registration_notes' => $request->registration_notes,
            'verified_at' => now(),
            'verified_by' => $user->id,
        ]);

        $cabang->user?->delete();
        // Pendaftaran ditolak harus mendaftar ulang dari awal, jadi berkas
        // lamanya tidak akan dipakai lagi dan tidak perlu menumpuk di storage.
        $cabang->deleteRegistrationDocuments();

        return redirect()
            ->route('cabang.travel', ['filter' => 'rejected'])
            ->with('success', "Pendaftaran cabang {$cabang->Penyelenggara} ditolak.");
    }

    public function showCabangDocument($id_cabang, string $type)
    {
        $cabang = CabangTravel::with('travel:id,dokumen_sk')->findOrFail($id_cabang);
        KabupatenResourceGuard::authorizeCabang(auth()->user(), $cabang);

        // SK pusat tidak diunggah cabang, dibaca langsung dari travel pusatnya.
        $path = StorageHelper::normalizePath(
            $type === 'sk_pusat' ? $cabang->skPusatPath() : $cabang->documentPath($type)
        );

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path));
    }

    private function buildCabangTravelListingQuery(Request $request, $user)
    {
        // dokumen_sk pusat dipakai tombol pratinjau "SK Pusat" di modal verifikasi.
        $query = CabangTravel::query()->with('travel:id,dokumen_sk')->select(
            'id_cabang',
            'travel_id',
            'Penyelenggara',
            'kabupaten',
            'pusat',
            'pimpinan_pusat',
            'alamat_pusat',
            'SK_BA',
            'tanggal',
            'pimpinan_cabang',
            'alamat_cabang',
            'telepon',
            'registration_status',
            'registration_notes',
            'dokumen_oss',
            'dokumen_akta',
            'dokumen_ktp_kepala',
            'dokumen_sk_du',
            'dokumen_rekomendasi',
            'catatan_rekomendasi',
            'recommended_at',
            'verified_at',
        );

        if ($user && $user->role === 'kabupaten') {
            $filters = KabupatenScopeFilter::filtersForUser($user);
            KabupatenScopeFilter::applyOnColumn($query, $filters, 'kabupaten');
        } elseif (! $user || $user->role !== 'admin') {
            $query->whereRaw('1 = 0');
        }

        $filter = $request->get('filter');

        if (in_array($filter, ['pending', 'menunggu_kanwil', 'approved', 'rejected'], true)) {
            $query->where('registration_status', $filter);
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('Penyelenggara', 'like', "%{$search}%")
                    ->orWhere('kabupaten', 'like', "%{$search}%")
                    ->orWhere('pusat', 'like', "%{$search}%")
                    ->orWhere('pimpinan_pusat', 'like', "%{$search}%")
                    ->orWhere('pimpinan_cabang', 'like', "%{$search}%")
                    ->orWhere('alamat_pusat', 'like', "%{$search}%")
                    ->orWhere('alamat_cabang', 'like', "%{$search}%")
                    ->orWhere('SK_BA', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%");
            });
        }

        return $query;
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
            return redirect()->back()->with('error', ExceptionMessageHelper::forUser($e, ExceptionMessageHelper::GENERIC_IMPORT));
        }
    }

    public function editCabangTravel($id_cabang)
    {
        $cabangTravel = CabangTravel::findOrFail($id_cabang);
        KabupatenResourceGuard::authorizeCabang(auth()->user(), $cabangTravel);

        return view('kanwil.editCabangTravel', $this->cabangFormData() + compact('cabangTravel'));
    }

    public function updateCabangTravel(Request $request, $id_cabang)
    {
        $cabangTravel = CabangTravel::findOrFail($id_cabang);
        KabupatenResourceGuard::authorizeCabang(auth()->user(), $cabangTravel);

        // Hanya field tervalidasi yang disimpan. Form edit tidak boleh jadi jalan
        // pintas untuk mengubah registration_status atau kolom verifikasi.
        $updateData = ValidationHelper::validate($request, self::cabangDataRules());

        if (auth()->user()->role === 'kabupaten') {
            $updateData['kabupaten'] = NtbKabupatenMap::normalize(auth()->user()->kabupaten);
        }

        $cabangTravel->update($updateData);

        return redirect()->route('cabang.travel')->with('success', 'Data cabang travel berhasil diperbarui.');
    }

    public function destroyCabangTravel($id_cabang)
    {
        $cabangTravel = CabangTravel::findOrFail($id_cabang);
        KabupatenResourceGuard::authorizeCabang(auth()->user(), $cabangTravel);
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
