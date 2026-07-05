<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengaduanRequest;
use App\Models\Pengaduan;
use App\Models\TravelCompany;
use App\Services\WorkQueueService;
use App\Support\PengaduanAttachmentStorage;
use Illuminate\Http\Request;
use PDF;
use Symfony\Component\HttpFoundation\Response;

class PengaduanController extends Controller
{
    public function __construct(
        private readonly WorkQueueService $workQueueService,
    ) {
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all travel companies
            $travels = TravelCompany::all();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten users can only see travel companies in their area
            $travels = TravelCompany::where('kab_kota', $user->kabupaten)->get();
        } else {
            // Other roles see empty data
            $travels = collect();
        }
        
        return view('pengaduan.create', compact('travels'));
    }

    public function store(StorePengaduanRequest $request)
    {

    return $this->persistPengaduan($request);
    }

    /**
     * Store pengaduan from public form (no authentication required)
     */
    public function storePublic(StorePengaduanRequest $request)
    {
        return $this->persistPengaduan($request);
    }

    private function persistPengaduan(StorePengaduanRequest $request)
    {
        $validated = $request->validated();

        $berkasPath = PengaduanAttachmentStorage::store(
            $request->file('berkas_aduan')
        );

        $pengaduan = Pengaduan::create([
            'nama_pengadu' => $validated['nama_pengadu'],
            'travels_id' => $validated['travels_id'],
            'hal_aduan' => $validated['hal_aduan'],
            'berkas_aduan' => $berkasPath,
            'status' => 'pending',
        ]);

        $this->workQueueService->handlePengaduanCreated($pengaduan);

        return redirect()
            ->back()
            ->with('success', 'Pengaduan berhasil dikirim! Kami akan memproses pengaduan Anda segera.');
    }

    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all pengaduan
            $pengaduan = Pengaduan::with('travel')->get();
            $pengaduanQuery = Pengaduan::query();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten users can only see pengaduan from travel in their area
            $pengaduan = Pengaduan::with('travel')
                ->whereHas('travel', function($query) use ($user) {
                    $query->where('kab_kota', $user->kabupaten);
                })->get();
            $pengaduanQuery = Pengaduan::whereHas('travel', function($query) use ($user) {
                $query->where('kab_kota', $user->kabupaten);
            });
        } else {
            // Other roles see empty data
            $pengaduan = collect();
            $pengaduanQuery = Pengaduan::whereRaw('1 = 0'); // Empty query
        }
        
        // Calculate statistics
        $stats = [
            'total' => $pengaduanQuery->count(),
            'pending' => $pengaduanQuery->where('status', 'pending')->count(),
            'in_progress' => $pengaduanQuery->where('status', 'in_progress')->count(),
            'completed' => $pengaduanQuery->where('status', 'completed')->count(),
            'rejected' => $pengaduanQuery->where('status', 'rejected')->count(),
        ];
        
        return view('pengaduan.index', compact('pengaduan', 'stats'));
    }

    public function detail($id)
    {
        $pengaduan = Pengaduan::with('travel')->findOrFail($id);
        return view('pengaduan.detail', compact('pengaduan'));
    }

    /**
     * Update pengaduan status (admin/kabupaten only)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes ?? '',
            'processed_by' => auth()->id(),
        ]);

        if (in_array($request->status, ['completed', 'rejected'], true)) {
            $this->workQueueService->handlePengaduanResolved($pengaduan);
        }

        // If status is completed, generate PDF
        if ($request->status === 'completed') {
            $pengaduan->update([
                'completed_at' => now(),
            ]);
            $this->generatePDF($pengaduan);
        }

        return response()->json(['success' => true, 'message' => 'Status pengaduan berhasil diperbarui.']);
    }

    /**
     * Generate PDF for completed pengaduan
     */
    private function generatePDF($pengaduan)
    {
        // Generate PDF using DomPDF
        $pdf = PDF::loadView('pengaduan.pdf', compact('pengaduan'));
        
        // Create directory if not exists
        $directory = storage_path('app/public/pengaduan_pdf');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Save PDF file
        $filename = 'pengaduan_' . $pengaduan->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdfPath = 'pengaduan_pdf/' . $filename;
        
        // Save PDF to storage
        $pdf->save(storage_path('app/public/' . $pdfPath));
        
        // Update database with PDF path
        $pengaduan->update(['pdf_output' => $pdfPath]);
    }

    /**
     * Download lampiran pengaduan (admin only, tidak via /storage/ publik).
     */
    public function downloadBerkas(int $id): Response
    {
        $pengaduan = Pengaduan::findOrFail($id);

        if (! $pengaduan->berkas_aduan) {
            abort(404);
        }

        $path = PengaduanAttachmentStorage::resolvePath($pengaduan->berkas_aduan);

        if ($path === null) {
            abort(404);
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = 'berkas_pengaduan_'.$pengaduan->id.'.'.$extension;

        return response()->file($path, [
            'Content-Type' => PengaduanAttachmentStorage::contentType($path),
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Download PDF for completed pengaduan
     */
    public function downloadPDF($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);
        
        if ($pengaduan->status !== 'completed') {
            return redirect()->back()->with('error', 'PDF hanya tersedia untuk pengaduan yang telah selesai.');
        }

        if (!$pengaduan->pdf_output) {
            return redirect()->back()->with('error', 'PDF belum tersedia.');
        }

        $path = storage_path('app/public/' . $pengaduan->pdf_output);
        
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan.');
        }

        return response()->download($path);
    }

    /**
     * Public view for completed pengaduan
     */
    public function publicView(string $token)
    {
        $pengaduan = Pengaduan::with('travel')->where('public_token', $token)->firstOrFail();
        
        if ($pengaduan->status !== 'completed') {
            abort(404);
        }

        return view('pengaduan.public-detail', compact('pengaduan'));
    }

    /**
     * API: Get completed pengaduan for public riwayat tab (paginated).
     */
    public function getCompletedPengaduan(Request $request)
    {
        try {
            $perPage = min(max((int) $request->integer('per_page', 6), 1), 20);
            $page = max((int) $request->integer('page', 1), 1);

            $paginator = Pengaduan::query()
                ->with(['travel:id,Penyelenggara'])
                ->select('id', 'travels_id', 'hal_aduan', 'completed_at', 'public_token')
                ->where('status', 'completed')
                ->orderByDesc('completed_at')
                ->paginate(perPage: $perPage, page: $page);

            return response()->json([
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getCompletedPengaduan: '.$e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data pengaduan',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Public download PDF for completed pengaduan (no authentication required)
     */
    public function downloadPDFPublic(string $token)
    {
        $pengaduan = Pengaduan::with('travel')->where('public_token', $token)->firstOrFail();
        
        if ($pengaduan->status !== 'completed') {
            abort(404);
        }

        if (!$pengaduan->pdf_output) {
            abort(404);
        }

        $path = storage_path('app/public/' . $pengaduan->pdf_output);
        
        if (!file_exists($path)) {
            abort(404);
        }

        $filename = 'pengaduan_' . $pengaduan->public_token . '_' . date('Y-m-d') . '.pdf';
        
        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
