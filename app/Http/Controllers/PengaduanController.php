<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use App\Models\TravelCompany;

class PengaduanController extends Controller
{
    public function create()
    {
        $travels = TravelCompany::all();
        return view('pengaduan.create', compact('travels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pengadu' => 'required|string|max:255',
            'travels_id' => 'required|exists:travels,id',
            'hal_aduan' => 'required|string',
            'berkas_aduan' => 'nullable|file|max:500',
        ]);

        $berkasPath = null;
        if ($request->hasFile('berkas_aduan')) {
            $berkasPath = $request->file('berkas_aduan')->store('berkas_aduan', 'public');
        }

        Pengaduan::create([
            'nama_pengadu' => $request->nama_pengadu,
            'travels_id' => $request->travels_id,
            'hal_aduan' => $request->hal_aduan,
            'berkas_aduan' => $berkasPath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengaduan berhasil dikirim! Kami akan memproses pengaduan Anda segera.');
    }

    public function index()
    {
        $user = auth()->user();
        
        // Admin can see all pengaduan
        if ($user->role === 'admin') {
            $pengaduan = Pengaduan::with('travel')->get();
        } else {
            // Kabupaten users can see pengaduan in their area
            // For now, show all data but this can be filtered by area later
            $pengaduan = Pengaduan::with('travel')->get();
        }
        
        return view('pengaduan.index', compact('pengaduan'));
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
            'admin_notes' => $request->admin_notes,
            'processed_by' => auth()->id(),
        ]);

        // If status is completed, generate PDF
        if ($request->status === 'completed') {
            $pengaduan->update([
                'completed_at' => now(),
            ]);
            $this->generatePDF($pengaduan);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Status pengaduan berhasil diperbarui.']);
        }
        
        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    /**
     * Generate PDF for completed pengaduan
     */
    private function generatePDF($pengaduan)
    {
        // Generate PDF content
        $pdfContent = view('pengaduan.pdf', compact('pengaduan'))->render();
        
        // Save PDF file
        $filename = 'pengaduan_' . $pengaduan->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdfPath = 'pengaduan_pdf/' . $filename;
        
        // For now, we'll just store the filename
        // In production, you'd use a PDF library like DomPDF or TCPDF
        $pengaduan->update(['pdf_output' => $pdfPath]);
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
    public function publicView($id)
    {
        $pengaduan = Pengaduan::with('travel')->findOrFail($id);
        
        if ($pengaduan->status !== 'completed') {
            abort(404, 'Pengaduan belum selesai diproses.');
        }

        return view('pengaduan.public-detail', compact('pengaduan'));
    }

    /**
     * API: Get completed pengaduan for modal
     */
    public function getCompletedPengaduan()
    {
        $completedPengaduan = Pengaduan::with('travel')
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();

        return response()->json($completedPengaduan);
    }
}
