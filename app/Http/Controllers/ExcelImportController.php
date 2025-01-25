<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataImport;
use App\Models\Data;

class ExcelImportController extends Controller
{
    public function importForm()
    {
        return view('kanwil.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new DataImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data Imported Successfully');
    }
}
