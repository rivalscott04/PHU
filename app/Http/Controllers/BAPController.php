<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BAP;
use Illuminate\Http\Request;

class BAPController extends Controller
{
    public function showFormBAP()
    {
        return view('travel.pengajuanBAP');
    }

    public function printBAP($id)
    {
        $data = BAP::findOrFail($id);

        $yearInWords = $this->nomorKata(now()->year);

        $formattedDate = Carbon::parse($data->datetime)->translatedFormat('d F Y');
        $formattedReturnDate = Carbon::parse($data->returndate)->translatedFormat('d F Y');

        return view('travel.printBAP', [
            'data' => $data,
            'yearInWords' => $yearInWords,
            'formattedDate' => $formattedDate,
            'formattedReturnDate' => $formattedReturnDate
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
        $data = BAP::all();

        return view('travel.listBAP', ['data' => $data]);
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

        // Debugging: Tampilkan data sebelum dikirim
        dump($request->all());

        // Simpan data ke dalam database
        BAP::create($request->all());

        // Redirect ke halaman yang diinginkan setelah data disimpan
        return redirect()->route('form')->with('success', 'Data berhasil disimpan.');
    }
}
