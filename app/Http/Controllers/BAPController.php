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

        $year = Carbon::parse($data->created_at)->year;

        $yearInWords = method_exists($this, 'nomorKata') ? $this->nomorKata($year) : $year;

        $dayName = Carbon::parse($data->created_at)->translatedFormat('l');
        $day = Carbon::parse($data->created_at)->translatedFormat('d');
        $monthYear = Carbon::parse($data->created_at)->translatedFormat('F Y');

        $formattedDate = Carbon::parse($data->datetime)->translatedFormat('d F Y');
        $formattedReturnDate = Carbon::parse($data->returndate)->translatedFormat('d F Y');

        $month = Carbon::parse($data->datetime)->format('m');

        return view('travel.printBAP', [
            'data' => $data,
            'yearInWords' => $yearInWords,
            'dayName' => $dayName,
            'day' => $day,
            'monthYear' => $monthYear,
            'formattedDate' => $formattedDate,
            'formattedReturnDate' => $formattedReturnDate,
            'month' => $month
        ]);
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
            return $words[floor($number / 10) * 10] . ' ' . $words[$number % 10];
        } elseif ($number < 1000) {
            return $words[floor($number / 100) * 100] . ' ' . $this->nomorKata($number % 100);
        } elseif ($number < 1000000) {
            return $this->nomorKata(floor($number / 1000)) . ' ribu ' . $this->nomorKata($number % 1000);
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
            // Admin dan kabupaten bisa melihat semua BAP yang tidak pending
            $data = BAP::where('status', '<>', 'pending')->get();
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
        $request->validate([
            'status' => 'required|in:pending,diajukan,diproses,diterima',
        ]);

        $data = BAP::findOrFail($id);
        $data->status = $request->status;
        $data->save();

        return redirect()->route('bap')->with('success', 'Status berhasil diubah.');
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
}
