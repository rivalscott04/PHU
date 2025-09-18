<?php

namespace App\Http\Controllers;

use App\Models\Jamaah;
use App\Exports\JamaahExport;
use App\Exports\JamaahUmrahExport;
use App\Exports\JamaahHajiExport;
use Illuminate\Http\Request;
use App\Imports\JamaahImport;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;

class JamaahController extends Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new JamaahExport, 'template_jamaah.xlsx');
    }

    public function indexHaji()
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            // User (travel) hanya bisa melihat jamaah dari kabupatennya
            $travel = $user->travel;
            if (!$travel || $travel->Status !== 'PIHK') {
                return redirect()->route('jamaah.umrah')
                    ->with('error', 'Travel Anda tidak memiliki izin untuk mengelola jamaah haji!');
            }
            $jamaah = Jamaah::where('jenis_jamaah', 'haji')
                ->whereHas('travel', function ($query) use ($user) {
                    $query->where('kab_kota', $user->kabupaten);
                })->get();
            $groupedJamaah = null;
        } else if ($user->role === 'kabupaten') {
            // Kabupaten hanya bisa melihat jamaah dari kabupatennya
            $jamaah = Jamaah::where('jenis_jamaah', 'haji')
                ->whereHas('travel', function ($query) use ($user) {
                    $query->where('kab_kota', $user->kabupaten);
                })->get();
            $groupedJamaah = null;
        } else if ($user->role === 'admin') {
            // Admin bisa melihat semua jamaah, dikelompokkan berdasarkan travel
            $jamaah = collect(); // Empty for admin view
            $groupedJamaah = Jamaah::where('jenis_jamaah', 'haji')
                ->with('travel')
                ->get()
                ->groupBy('travel_id');
        } else {
            $jamaah = collect();
            $groupedJamaah = null;
        }

        return view('jamaah.haji.index', compact('jamaah', 'groupedJamaah'));
    }

    public function indexUmrah()
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            // User (travel) hanya bisa melihat jamaah dari kabupatennya
            $jamaah = Jamaah::where('jenis_jamaah', 'umrah')
                ->whereHas('travel', function ($query) use ($user) {
                    $query->where('kab_kota', $user->kabupaten);
                })->get();
            $groupedJamaah = null;
        } else if ($user->role === 'kabupaten') {
            // Kabupaten hanya bisa melihat jamaah dari kabupatennya
            $jamaah = Jamaah::where('jenis_jamaah', 'umrah')
                ->whereHas('travel', function ($query) use ($user) {
                    $query->where('kab_kota', $user->kabupaten);
                })->get();
            $groupedJamaah = null;
        } else if ($user->role === 'admin') {
            // Admin bisa melihat semua jamaah, dikelompokkan berdasarkan travel
            $jamaah = collect(); // Empty for admin view
            $groupedJamaah = Jamaah::where('jenis_jamaah', 'umrah')
                ->with('travel')
                ->get()
                ->groupBy('travel_id');
        } else {
            $jamaah = collect();
            $groupedJamaah = null;
        }

        return view('jamaah.umrah.index', compact('jamaah', 'groupedJamaah'));
    }

    public function createHaji()
    {
        $user = auth()->user();
        $isAdminOrKabupaten = in_array($user->role, ['admin', 'kabupaten']);

        if (!$isAdminOrKabupaten) {
            $travel = $user->travel;
            if (!$travel || $travel->Status !== 'PIHK') {
                return redirect()->route('jamaah.umrah')
                    ->with('error', 'Travel Anda tidak memiliki izin untuk mengelola jamaah haji!');
            }
        }
        return view('jamaah.haji.create');
    }

    public function createUmrah()
    {
        return view('jamaah.umrah.create');
    }

    public function storeHaji(Request $request)
    {
        $request->validate([
            'nik' => 'required|max:16',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:15|regex:/^08/',
        ], [
            'nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
        ]);

        try {
            $user = auth()->user();
            $jamaahData = $request->all();
            $jamaahData['jenis_jamaah'] = 'haji';
            $jamaahData['user_id'] = $user->id;
            $jamaahData['travel_id'] = $user->travel_id;

            Jamaah::create($jamaahData);
            return redirect()->route('jamaah.haji')->with('success', 'Data jamaah haji berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function storeUmrah(Request $request)
    {
        $request->validate([
            'nik' => 'required|max:16',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:15|regex:/^08/',
        ], [
            'nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
        ]);

        try {
            $user = auth()->user();
            $jamaahData = $request->all();
            $jamaahData['jenis_jamaah'] = 'umrah';
            $jamaahData['user_id'] = $user->id;
            $jamaahData['travel_id'] = $user->travel_id;

            Jamaah::create($jamaahData);
            return redirect()->route('jamaah.umrah')->with('success', 'Data jamaah umrah berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $jamaah = Jamaah::findOrFail($id);
        return view('jamaah.edit', compact('jamaah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:15|regex:/^08/',
        ], [
            'nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
        ]);

        try {
            $jamaah = Jamaah::findOrFail($id);
            $jamaah->update($request->only(['nama', 'alamat', 'nomor_hp']));

            return redirect()->route('jamaah.detail', $id)
                ->with('success', 'Data jamaah berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $jamaah = Jamaah::findOrFail($id);
            $jenisJamaah = $jamaah->jenis_jamaah;
            $jamaah->delete();

            $redirectRoute = ($jenisJamaah === 'haji') ? 'jamaah.haji' : 'jamaah.umrah';
            return redirect()->route($redirectRoute)
                ->with('success', 'Data jamaah ' . $jenisJamaah . ' berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new JamaahImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $jamaah = Jamaah::findOrFail($id);
        return view('jamaah.detail', compact('jamaah'));
    }

    public function export()
    {
        return Excel::download(new JamaahExport, 'jamaah.xlsx');
    }

    public function exportUmrah(Request $request)
    {
        $format = $request->get('format', 'excel');
        $type = $request->get('type', 'global');
        $travelId = $request->get('travel_id');

        if ($format === 'pdf') {
            return $this->exportUmrahPDF($request);
        }

        if ($type === 'travel' && $travelId) {
            // Export specific travel
            $jamaah = Jamaah::where('jenis_jamaah', 'umrah')
                ->where('travel_id', $travelId)
                ->with('travel')
                ->get();

            $travel = $jamaah->first()->travel ?? null;
            $filename = $travel ? 'jamaah_umrah_' . str_replace(' ', '_', $travel->Penyelenggara) . '.xlsx' : 'jamaah_umrah_travel.xlsx';

            return Excel::download(new JamaahUmrahExport($jamaah, false), $filename);
        } else {
            // Export global with separators
            $jamaah = Jamaah::where('jenis_jamaah', 'umrah')
                ->with('travel')
                ->get()
                ->groupBy('travel_id');

            $filename = 'jamaah_umrah_global_' . now()->format('Y-m-d') . '.xlsx';

            return Excel::download(new JamaahUmrahExport($jamaah, true), $filename);
        }
    }

    public function exportHaji(Request $request)
    {
        $format = $request->get('format', 'excel');
        $type = $request->get('type', 'global');
        $travelId = $request->get('travel_id');

        if ($format === 'pdf') {
            return $this->exportHajiPDF($request);
        }

        if ($type === 'travel' && $travelId) {
            // Export specific travel
            $jamaah = Jamaah::where('jenis_jamaah', 'haji')
                ->where('travel_id', $travelId)
                ->with('travel')
                ->get();

            $travel = $jamaah->first()->travel ?? null;
            $filename = $travel ? 'jamaah_haji_' . str_replace(' ', '_', $travel->Penyelenggara) . '.xlsx' : 'jamaah_haji_travel.xlsx';

            return Excel::download(new JamaahHajiExport($jamaah, false), $filename);
        } else {
            // Export global with separators
            $jamaah = Jamaah::where('jenis_jamaah', 'haji')
                ->with('travel')
                ->get()
                ->groupBy('travel_id');

            $filename = 'jamaah_haji_global_' . now()->format('Y-m-d') . '.xlsx';

            return Excel::download(new JamaahHajiExport($jamaah, true), $filename);
        }
    }

    public function exportUmrahPDF(Request $request)
    {
        $type = $request->get('type', 'global');
        $travelId = $request->get('travel_id');

        if ($type === 'travel' && $travelId) {
            // Export specific travel
            $jamaah = Jamaah::where('jenis_jamaah', 'umrah')
                ->where('travel_id', $travelId)
                ->with('travel')
                ->get();

            $travel = $jamaah->first()->travel ?? null;
            $filename = $travel ? 'jamaah_umrah_' . str_replace(' ', '_', $travel->Penyelenggara) . '.pdf' : 'jamaah_umrah_travel.pdf';

            return $this->generatePDF($jamaah, false, 'umrah', $filename);
        } else {
            // Export global with separators
            $jamaah = Jamaah::where('jenis_jamaah', 'umrah')
                ->with('travel')
                ->get()
                ->groupBy('travel_id');

            $filename = 'jamaah_umrah_global_' . now()->format('Y-m-d') . '.pdf';

            return $this->generatePDF($jamaah, true, 'umrah', $filename);
        }
    }

    public function exportHajiPDF(Request $request)
    {
        $type = $request->get('type', 'global');
        $travelId = $request->get('travel_id');

        if ($type === 'travel' && $travelId) {
            // Export specific travel
            $jamaah = Jamaah::where('jenis_jamaah', 'haji')
                ->where('travel_id', $travelId)
                ->with('travel')
                ->get();

            $travel = $jamaah->first()->travel ?? null;
            $filename = $travel ? 'jamaah_haji_' . str_replace(' ', '_', $travel->Penyelenggara) . '.pdf' : 'jamaah_haji_travel.pdf';

            return $this->generatePDF($jamaah, false, 'haji', $filename);
        } else {
            // Export global with separators
            $jamaah = Jamaah::where('jenis_jamaah', 'haji')
                ->with('travel')
                ->get()
                ->groupBy('travel_id');

            $filename = 'jamaah_haji_global_' . now()->format('Y-m-d') . '.pdf';

            return $this->generatePDF($jamaah, true, 'haji', $filename);
        }
    }

    private function generatePDF($data, $isGlobal, $type, $filename)
    {
        $html = $this->generatePDFHTML($data, $isGlobal, $type);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', public_path());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    private function generatePDFHTML($data, $isGlobal, $type)
    {
        $jenisJamaah = ucfirst($type);
        $title = "DATA JAMAAH {$jenisJamaah}";

        // Convert logo to base64
        $logoPath = public_path('images/kemenag.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        $html = '<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>' . $title . '</title>
        <style>
            @page {
                size: A4;
                margin: 15mm 15mm 35mm 15mm;
            }

            body {
                margin: 0;
                font-family: Arial, sans-serif;
                position: relative;
                min-height: 100vh;
            }

            .page-container {
                position: relative;
                min-height: calc(100vh - 50mm);
                padding-bottom: 20mm;
            }

            .header {
                text-align: center;
                font-size: 12pt;
                line-height: 1.2;
                margin-bottom: 5mm;
            }

            .logo {
                height: 90px;
                width: auto;
                position: absolute;
                left: 0;
                top: 0;
            }

            .letterhead {
                border-bottom: 2px solid black;
                padding-bottom: 4mm;
                margin-bottom: 5mm;
            }

            .title {
                text-align: center;
                font-weight: bold;
                margin-top: 2mm;
                margin-bottom: 3mm;
                font-size: 14pt;
                line-height: 1.2;
            }

            .content {
                margin: 2mm 0;
                font-size: 11pt;
                line-height: 1.2;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 3mm;
            }

            th, td {
                border: 1px solid #000;
                padding: 2mm;
                text-align: left;
                font-size: 10pt;
            }

            th {
                background-color: #f0f0f0;
                font-weight: bold;
            }

            .separator {
                background-color: #e0e0e0;
                font-weight: bold;
                text-align: center;
                padding: 4mm 2mm;
            }

            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: left;
                font-size: 10pt;
                font-weight: bold;
                border-top: 1px solid #000;
                padding: 3mm 0mm 5mm 0mm;
                background: white;
                height: 5mm;
                z-index: 1000;
            }

            /* Tambahan CSS untuk memastikan footer muncul di semua halaman */
            .footer::before {
                content: "";
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 20mm;
                background: white;
                z-index: -1;
            }

            .page-break {
                page-break-before: always;
            }

            .ppiu-section {
                page-break-inside: avoid;
                margin-bottom: 5mm;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }

            /* Styling khusus untuk halaman pertama */
            .first-page {
                margin-bottom: 25mm;
            }
        </style>
    </head>
    <body>
        <!-- Footer harus ditempatkan di awal body agar muncul di semua halaman -->
        <div class="footer">
            Dokumen ini dibuat otomatis dari sistem PHU
        </div>

        <div class="page-container first-page">
            <div class="letterhead">
                <img src="' . $logoBase64 . '" alt="Logo" class="logo">
                <div class="header">
                    <strong>KEMENTERIAN AGAMA REPUBLIK INDONESIA<br>
                        KANTOR WILAYAH KEMENTERIAN AGAMA<br>
                        PROVINSI NUSA TENGGARA BARAT<br></strong>
                    <span style="font-size: 12pt;">JL. Udayana No. 6 Mataram<br>
                        Telp. (0370) 622317 Faksimili (0370) 622317<br>
                        Website : www.ntb.Kemenag.go.id
                    </span>
                </div>
            </div>

            <div class="title">
                ' . $title . '<br>
                <span style="font-size: 12pt;">Tanggal: ' . now()->format('d F Y') . '</span>
            </div>

            <div class="content">';

        if ($isGlobal) {
            $isFirstSection = true;
            foreach ($data as $travelId => $jamaahGroup) {
                if (!$isFirstSection) {
                    $html .= '<div class="page-break"></div>
                    <div class="page-container">';
                }

                $travel = $jamaahGroup->first()->travel;
                $totalJamaah = $jamaahGroup->count();

                $html .= '
                    <div class="ppiu-section">
                        <table>
                            <thead>
                                <tr class="separator">
                                    <td colspan="5">
                                        <strong>PPIU: ' . ($travel->Penyelenggara ?? 'Tidak Diketahui') . '</strong><br>
                                        <small>Kabupaten: ' . ($travel->kab_kota ?? 'Tidak Diketahui') . ' | Total Jamaah: ' . $totalJamaah . ' | Status: ' . ($travel->Status ?? 'N/A') . '</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jamaah</th>
                                    <th>Alamat</th>
                                    <th>No HP</th>
                                    <th>NIK</th>
                                </tr>
                            </thead>
                            <tbody>';

                foreach ($jamaahGroup as $index => $jamaah) {
                    $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . htmlspecialchars($jamaah->nama) . '</td>
                        <td>' . htmlspecialchars($jamaah->alamat) . '</td>
                        <td>' . htmlspecialchars($jamaah->nomor_hp) . '</td>
                        <td>' . htmlspecialchars($jamaah->nik) . '</td>
                    </tr>';
                }

                $html .= '
                            </tbody>
                        </table>
                    </div>';

                if (!$isFirstSection) {
                    $html .= '</div>';
                }

                $isFirstSection = false;
            }
        } else {
            $html .= '
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jamaah</th>
                        <th>Alamat</th>
                        <th>No HP</th>
                        <th>NIK</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($data as $index => $jamaah) {
                $html .= '
                <tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . htmlspecialchars($jamaah->nama) . '</td>
                    <td>' . htmlspecialchars($jamaah->alamat) . '</td>
                    <td>' . htmlspecialchars($jamaah->nomor_hp) . '</td>
                    <td>' . htmlspecialchars($jamaah->nik) . '</td>
                </tr>';
            }

            $html .= '
                </tbody>
            </table>';
        }

        $html .= '
            </div>
        </div>
    </body>
    </html>';

        return $html;
    }
}
