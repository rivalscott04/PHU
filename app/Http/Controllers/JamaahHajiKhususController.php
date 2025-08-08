<?php

namespace App\Http\Controllers;

use App\Models\JamaahHajiKhusus;
use App\Models\TravelCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

            $jamaahHajiKhusus = collect(); // Empty for admin view
            $groupedJamaahHajiKhusus = $query->latest()->get()->groupBy('travel_id');
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
            'no_ktp' => 'required|string|size:16|unique:jamaah_haji_khusus,no_ktp',
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
    public function show(JamaahHajiKhusus $jamaahHajiKhusus)
    {
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
    public function edit(JamaahHajiKhusus $jamaahHajiKhusus)
    {
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
    public function update(Request $request, JamaahHajiKhusus $jamaahHajiKhusus)
    {
        $user = Auth::user();
        
        // Check access
        if ($user->role === 'user' && $jamaahHajiKhusus->travel_id !== $user->travel->id) {
            return redirect()->route('jamaah.haji-khusus.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'required|string|size:16|unique:jamaah_haji_khusus,no_ktp,' . $jamaahHajiKhusus->id,
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
    public function destroy(JamaahHajiKhusus $jamaahHajiKhusus)
    {
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
    public function updateStatus(Request $request, JamaahHajiKhusus $jamaahHajiKhusus)
    {
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
    public function export()
    {
        $user = Auth::user();
        $query = JamaahHajiKhusus::with('travel');

        // Filter based on user role
        if ($user->role === 'user' && $user->travel) {
            $query->where('travel_id', $user->travel->id);
        }

        $jamaahHajiKhusus = $query->get();

        // Generate Excel/CSV export
        // Implementation depends on your export library
        return response()->json([
            'message' => 'Export functionality will be implemented',
            'data' => $jamaahHajiKhusus
        ]);
    }

    /**
     * Verify bukti setor bank
     */
    public function verifyBuktiSetor(Request $request, JamaahHajiKhusus $jamaahHajiKhusus)
    {
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
    public function assignPorsiNumber(Request $request, JamaahHajiKhusus $jamaahHajiKhusus)
    {
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
