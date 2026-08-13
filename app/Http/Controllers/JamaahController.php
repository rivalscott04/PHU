<?php

namespace App\Http\Controllers;

use App\Helpers\ExceptionMessageHelper;
use App\Helpers\ValidationHelper;
use App\Support\OperatorScope;
use App\Models\Jamaah;
use App\Exports\JamaahExport;
use App\Exports\JamaahUmrahExport;
use App\Exports\JamaahHajiExport;
use Illuminate\Http\Request;
use App\Imports\JamaahImport;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use App\Support\ExportFilename;
use App\Support\JamaahExportScope;
use App\Support\JamaahListingQuery;
use App\Support\KanwilContact;
use App\Support\KabupatenResourceGuard;

class JamaahController extends Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new JamaahExport, 'template_jamaah.xlsx');
    }

    public function indexHaji(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            $travel = $user->travel;
            if (! $travel || $travel->Status !== 'PIHK') {
                return redirect()->route('jamaah.umrah')
                    ->with('error', 'Travel Anda tidak memiliki izin untuk mengelola jamaah haji!');
            }
        }

        return $this->renderJamaahListing($request, 'haji', 'jamaah.haji.index', 'jamaah.haji');
    }

    private function renderJamaahListing(Request $request, string $jenis, string $viewName, string $listingRoute)
    {
        $user = auth()->user();
        $showTravelColumn = in_array($user->role, ['admin', 'kabupaten'], true);

        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $jamaah = $this->buildJamaahListingQuery($jenis, $request, $user)
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tableBody' => view('jamaah.partials.table-body', compact('jamaah', 'showTravelColumn'))->render(),
                'pagination' => view('jamaah.partials.pagination', compact('jamaah'))->render(),
                'pagination_info' => [
                    'from' => $jamaah->firstItem(),
                    'to' => $jamaah->lastItem(),
                    'total' => $jamaah->total(),
                    'current_page' => $jamaah->currentPage(),
                    'last_page' => $jamaah->lastPage(),
                ],
                'filters' => [
                    'search' => $request->get('search'),
                    'travel_id' => $request->get('travel_id'),
                ],
            ]);
        }

        $travelOptions = JamaahListingQuery::travelOptions($jenis, $user);

        return view($viewName, compact(
            'jamaah',
            'listingRoute',
            'showTravelColumn',
            'travelOptions',
        ));
    }

    private function buildJamaahListingQuery(string $jenis, Request $request, $user)
    {
        return JamaahListingQuery::build($jenis, $request, $user);
    }

    public function indexUmrah(Request $request)
    {
        return $this->renderJamaahListing($request, 'umrah', 'jamaah.umrah.index', 'jamaah.umrah');
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
        $user = auth()->user();

        ValidationHelper::validate($request, [
            'nik' => ValidationHelper::nikRules(),
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => ValidationHelper::nomorHpRules(),
        ]);

        try {
            $jamaahData = $request->all();
            $jamaahData['jenis_jamaah'] = 'haji';
            $jamaahData['user_id'] = $user->id;
            $jamaahData = array_merge($jamaahData, OperatorScope::ownerColumns($user));

            Jamaah::create($jamaahData);
            return redirect()->route('jamaah.haji')->with('success', 'Data jamaah haji berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', ExceptionMessageHelper::forUser($e, ExceptionMessageHelper::GENERIC_SAVE));
        }
    }

    public function storeUmrah(Request $request)
    {
        $user = auth()->user();

        ValidationHelper::validate($request, [
            'nik' => ValidationHelper::nikRules(),
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => ValidationHelper::nomorHpRules(),
        ]);

        try {
            $jamaahData = $request->all();
            $jamaahData['jenis_jamaah'] = 'umrah';
            $jamaahData['user_id'] = $user->id;
            $jamaahData = array_merge($jamaahData, OperatorScope::ownerColumns($user));

            Jamaah::create($jamaahData);
            return redirect()->route('jamaah.umrah')->with('success', 'Data jamaah umrah berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', ExceptionMessageHelper::forUser($e, ExceptionMessageHelper::GENERIC_SAVE));
        }
    }

    public function edit($id)
    {
        $jamaah = Jamaah::findOrFail($id);
        KabupatenResourceGuard::authorizeJamaah(auth()->user(), $jamaah);

        return view('jamaah.edit', compact('jamaah'));
    }

    public function update(Request $request, $id)
    {
        ValidationHelper::validate($request, [
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nomor_hp' => ValidationHelper::nomorHpRules(),
        ]);

        try {
            $jamaah = Jamaah::findOrFail($id);
            KabupatenResourceGuard::authorizeJamaah(auth()->user(), $jamaah);
            $jamaah->update($request->only(['nama', 'alamat', 'nomor_hp']));

            return redirect()->route('jamaah.detail', $id)
                ->with('success', 'Data jamaah berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', ExceptionMessageHelper::forUser($e, ExceptionMessageHelper::GENERIC_SAVE));
        }
    }

    public function destroy($id)
    {
        try {
            $jamaah = Jamaah::findOrFail($id);
            KabupatenResourceGuard::authorizeJamaah(auth()->user(), $jamaah);
            $jenisJamaah = $jamaah->jenis_jamaah;
            $jamaah->delete();

            $redirectRoute = ($jenisJamaah === 'haji') ? 'jamaah.haji' : 'jamaah.umrah';
            return redirect()->route($redirectRoute)
                ->with('success', 'Data jamaah ' . $jenisJamaah . ' berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', ExceptionMessageHelper::forUser($e, ExceptionMessageHelper::GENERIC_SAVE));
        }
    }

    public function import(Request $request)
    {
        ValidationHelper::validate($request, [
            'file' => 'required|mimes:xlsx,xls',
            'jenis_jamaah' => 'required|in:haji,umrah',
        ]);

        try {
            Excel::import(new JamaahImport($request->jenis_jamaah), $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', ExceptionMessageHelper::forUser($e, ExceptionMessageHelper::GENERIC_SAVE));
        }
    }

    public function detail($id)
    {
        $jamaah = Jamaah::findOrFail($id);
        KabupatenResourceGuard::authorizeJamaah(auth()->user(), $jamaah);

        return view('jamaah.detail', compact('jamaah'));
    }

    public function export()
    {
        return Excel::download(new JamaahExport, 'jamaah.xlsx');
    }

    public function exportUmrah(Request $request)
    {
        return $this->exportJamaahByJenis($request, 'umrah', JamaahUmrahExport::class);
    }

    public function exportHaji(Request $request)
    {
        return $this->exportJamaahByJenis($request, 'haji', JamaahHajiExport::class);
    }

    public function exportUmrahPDF(Request $request)
    {
        return $this->exportJamaahPdfByJenis($request, 'umrah');
    }

    public function exportHajiPDF(Request $request)
    {
        return $this->exportJamaahPdfByJenis($request, 'haji');
    }

    private function exportJamaahByJenis(Request $request, string $jenis, string $exportClass)
    {
        if ($request->get('format') === 'pdf') {
            return $this->exportJamaahPdfByJenis($request, $jenis);
        }

        $scope = JamaahExportScope::forJamaah(auth()->user(), $request, $jenis);
        if ($scope['error']) {
            return back()->with('error', $scope['error']);
        }

        $filename = ExportFilename::jamaah($jenis, $scope['isGlobal'], $scope['travel'], 'xlsx');

        return Excel::download(new $exportClass($scope['data'], $scope['isGlobal']), $filename);
    }

    private function exportJamaahPdfByJenis(Request $request, string $jenis)
    {
        $scope = JamaahExportScope::forJamaah(auth()->user(), $request, $jenis);
        if ($scope['error']) {
            return back()->with('error', $scope['error']);
        }

        $filename = ExportFilename::jamaah($jenis, $scope['isGlobal'], $scope['travel'], 'pdf');

        return $this->generatePDF($scope['data'], $scope['isGlobal'], $jenis, $filename);
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
        $jenisJamaah = match ($type) {
            'haji-khusus' => 'Haji Khusus',
            default => ucfirst($type),
        };
        $title = 'DATA JAMAAH '.strtoupper($jenisJamaah);

        // Convert logo to base64
        $logoPath = public_path('images/logo_web.png');
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
            Dokumen ini dibuat otomatis dari PANTAU
        </div>

        <div class="page-container first-page">
            <div class="letterhead">
                <img src="' . $logoBase64 . '" alt="Logo" class="logo">
                <div class="header">
                    <strong>'.KanwilContact::letterheadTitleHtml().'<br></strong>
                    <span style="font-size: 12pt;">'.KanwilContact::letterheadContactHtml().'</span>
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
                                        <small>Kabupaten: ' . ($travel->kab_kota ?? 'Tidak Diketahui') . ' | Total Jamaah: ' . $totalJamaah . ' | Status: ' . ($travel->Status ?? 'Tidak Diketahui') . '</small>
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
