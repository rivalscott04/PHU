<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TravelCompany;
use App\Imports\UserTravelImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserManagementController extends Controller
{
    /**
     * Display a listing of kabupaten users
     */
    public function indexKabupaten()
    {
        $kabupatenUsers = User::where('role', 'kabupaten')->get();
        return view('admin.kabupaten.index', compact('kabupatenUsers'));
    }

    /**
     * Display a listing of travel users
     */
    public function indexTravel()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all travel users
            $travelUsers = User::where('role', 'user')->with('travel')->get();
        } else {
            // Kabupaten can only see travel users from their kabupaten
            $travelUsers = User::where('role', 'user')
                ->whereHas('travel', function($query) use ($user) {
                    $query->where('kab_kota', $user->kabupaten);
                })
                ->with('travel')
                ->get();
        }
        
        return view('admin.travel.index', compact('travelUsers'));
    }

    /**
     * Show the form for creating a new kabupaten user
     */
    public function createKabupaten()
    {
        return view('admin.kabupaten.create');
    }

    /**
     * Show the form for creating a new travel user
     */
    public function createTravel()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all travel companies
            $travelCompanies = \App\Models\TravelCompany::all();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten can only see travel companies from their kabupaten
            $travelCompanies = \App\Models\TravelCompany::where('kab_kota', $user->kabupaten)->get();
        } else {
            // Other roles see empty data
            $travelCompanies = collect();
        }
        
        return view('admin.travel.create', compact('travelCompanies'));
    }

    /**
     * Store a newly created kabupaten user
     */
    public function storeKabupaten(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nomor_hp' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal' => 'required|string|max:10',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'password' => Hash::make($request->password),
            'role' => 'kabupaten',
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal' => $request->postal,
            'is_password_changed' => 0,
        ]);

        return redirect()->route('kabupaten.index')->with('success', 'User Kabupaten berhasil ditambahkan!');
    }

    /**
     * Store a newly created travel user
     */
    public function storeTravel(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nomor_hp' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:5',
            'travel_id' => 'required|exists:travels,id',
        ]);

        // Check if kabupaten user is trying to create travel user for different kabupaten
        if ($user->role === 'kabupaten') {
            $travelCompany = \App\Models\TravelCompany::find($request->travel_id);
            if ($travelCompany->kab_kota !== $user->kabupaten) {
                return redirect()->back()->with('error', 'Anda hanya bisa membuat user travel untuk kabupaten Anda sendiri.');
            }
        }

        // Get travel company data for auto-fill
        $travelCompany = TravelCompany::find($request->travel_id);
        
        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'travel_id' => $request->travel_id,
            'kabupaten' => $travelCompany->kab_kota,
            'country' => 'Indonesia', // Default value
            'is_password_changed' => false,
        ]);

        return redirect()->route('travels.index')->with('success', 'User Travel berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();
        
        // Check if kabupaten user is trying to edit travel user from different kabupaten
        if ($currentUser->role === 'kabupaten' && $user->role === 'user') {
            if (!$user->travel || $user->travel->kab_kota !== $currentUser->kabupaten) {
                return redirect()->back()->with('error', 'Anda hanya bisa mengedit user travel dari kabupaten Anda sendiri.');
            }
        }
        
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();
        
        // Check if kabupaten user is trying to edit travel user from different kabupaten
        if ($currentUser->role === 'kabupaten' && $user->role === 'user') {
            if (!$user->travel || $user->travel->kab_kota !== $currentUser->kabupaten) {
                return redirect()->back()->with('error', 'Anda hanya bisa mengedit user travel dari kabupaten Anda sendiri.');
            }
        }
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'nomor_hp' => 'required|string|max:20|unique:users,nomor_hp,' . $id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal' => 'nullable|string|max:10',
        ]);

        $updateData = [
            'nama' => $request->nama,
            'email' => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal' => $request->postal,
        ];

        // Add travel_id for travel users
        if ($user->role === 'user' && $request->filled('travel_id')) {
            $updateData['travel_id'] = $request->travel_id;
        }

        $user->update($updateData);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
                'is_password_changed' => 0,
            ]);
        }

        $route = $user->role === 'kabupaten' ? 'kabupaten.index' : 'travels.index';
        return redirect()->route($route)->with('success', 'User berhasil diupdate!');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();
        
        // Check if kabupaten user is trying to delete travel user from different kabupaten
        if ($currentUser->role === 'kabupaten' && $user->role === 'user') {
            if (!$user->travel || $user->travel->kab_kota !== $currentUser->kabupaten) {
                return redirect()->back()->with('error', 'Anda hanya bisa menghapus user travel dari kabupaten Anda sendiri.');
            }
        }
        
        $user->delete();

        $route = $user->role === 'kabupaten' ? 'kabupaten.index' : 'travels.index';
        return redirect()->route($route)->with('success', 'User berhasil dihapus!');
    }

    /**
     * Show the form for importing travel users via Excel
     */
    public function importTravelForm()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin can see all travel companies
            $travelCompanies = TravelCompany::all();
        } else if ($user->role === 'kabupaten') {
            // Kabupaten can only see travel companies from their kabupaten
            $travelCompanies = TravelCompany::where('kab_kota', $user->kabupaten)->get();
        } else {
            // Other roles see empty data
            $travelCompanies = collect();
        }
        
        return view('admin.travel.import', compact('travelCompanies'));
    }

    /**
     * Handle Excel import for travel users
     */
    public function importTravelUsers(Request $request)
    {
        $request->validate([
            'travel_id' => 'required|exists:travels,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        $user = auth()->user();
        
        // Check if kabupaten user is trying to import for different kabupaten
        if ($user->role === 'kabupaten') {
            $travelCompany = TravelCompany::find($request->travel_id);
            if ($travelCompany->kab_kota !== $user->kabupaten) {
                return redirect()->back()->with('error', 'Anda hanya bisa import user untuk travel di kabupaten Anda sendiri.');
            }
        }

        try {
            DB::beginTransaction();

            // Create import instance with travel_id
            $import = new UserTravelImport($request->travel_id);
            
            // Import the Excel file
            Excel::import($import, $request->file('excel_file'));

            DB::commit();

            // Get import results
            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();

            // Prepare success message
            $message = "Import berhasil! {$successCount} user berhasil dibuat.";
            
            // Add errors to message if any
            if (!empty($errors)) {
                $message .= "\n\nError yang ditemukan:\n" . implode("\n", $errors);
            }

            return redirect()->route('travels.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('UserTravelImport error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download Excel template for travel users import
     */
    public function downloadTravelUserTemplate()
    {
        $filePath = public_path('template/templateuser.xlsx');
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Template file tidak ditemukan.');
        }
        
        return response()->download($filePath, 'Template_Import_User_Travel.xlsx');
    }
}
