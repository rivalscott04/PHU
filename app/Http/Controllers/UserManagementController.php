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
    public function indexKabupaten(Request $request)
    {
        // Base query for kabupaten users
        $query = User::where('role', 'kabupaten');
        
        // Apply search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_hp', 'like', "%{$search}%")
                  ->orWhere('kabupaten', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort fields
        $allowedSortFields = ['nama', 'email', 'nomor_hp', 'kabupaten', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        $query->orderBy($sortBy, $sortOrder);
        
        // Get all results (no pagination needed for small dataset)
        $kabupatenUsers = $query->get();

        // Handle AJAX requests
        if ($request->ajax()) {
            $tableBody = view('admin.kabupaten.partials.table-body', compact('kabupatenUsers'))->render();
            
            return response()->json([
                'success' => true,
                'tableBody' => $tableBody,
                'pagination' => '', // No pagination needed
                'pagination_info' => [
                    'from' => $kabupatenUsers->count() > 0 ? 1 : 0,
                    'to' => $kabupatenUsers->count(),
                    'total' => $kabupatenUsers->count(),
                    'current_page' => 1,
                    'last_page' => 1,
                ],
                'filters' => [
                    'search' => $request->get('search'),
                ]
            ]);
        }

        return view('admin.kabupaten.index', compact('kabupatenUsers'));
    }

    /**
     * Display a listing of travel users
     */
    public function indexTravel(Request $request)
    {
        $user = auth()->user();

        // Base query for travel users
        $query = User::where('role', 'user')->with('travel');
        
        // Apply role-based filtering
        if ($user->role === 'kabupaten') {
            // Kabupaten can only see travel users from their kabupaten
            $query->whereHas('travel', function ($q) use ($user) {
                $q->where('kab_kota', $user->kabupaten);
            });
        }
        
        // Apply search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_hp', 'like', "%{$search}%")
                  ->orWhereHas('travel', function ($travelQuery) use ($search) {
                      $travelQuery->where('Penyelenggara', 'like', "%{$search}%")
                                  ->orWhere('Pimpinan', 'like', "%{$search}%");
                  });
            });
        }
        
        // Apply travel company filter
        if ($request->filled('travel_company')) {
            $query->whereHas('travel', function ($q) use ($request) {
                $q->where('Penyelenggara', 'like', "%{$request->travel_company}%");
            });
        }
        
        // Apply kabupaten filter (only for admin)
        if ($user->role === 'admin' && $request->filled('kabupaten')) {
            $query->whereHas('travel', function ($q) use ($request) {
                $q->where('kab_kota', $request->kabupaten);
            });
        }
        
        // Apply travel status filter
        if ($request->filled('travel_status')) {
            $query->whereHas('travel', function ($q) use ($request) {
                $q->where('Status', $request->travel_status);
            });
        }
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort fields
        $allowedSortFields = ['nama', 'email', 'nomor_hp', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        $query->orderBy($sortBy, $sortOrder);
        
        // Get paginated results
        $perPage = $request->get('per_page', 15);
        $travelUsers = $query->paginate($perPage)->withQueryString();
        
        // Get filter options for dropdowns - optimized to avoid N+1
        $travelCompanies = TravelCompany::select('Penyelenggara')->distinct()->orderBy('Penyelenggara')->pluck('Penyelenggara');
        $kabupatens = TravelCompany::select('kab_kota')->distinct()->orderBy('kab_kota')->pluck('kab_kota');
        $travelStatuses = TravelCompany::select('Status')->distinct()->orderBy('Status')->pluck('Status');

        // Handle AJAX requests
        if ($request->ajax()) {
            $tableBody = view('admin.travel.partials.table-body', compact('travelUsers'))->render();
            $pagination = view('admin.travel.partials.pagination', compact('travelUsers'))->render();
            
            return response()->json([
                'success' => true,
                'tableBody' => $tableBody,
                'pagination' => $pagination,
                'pagination_info' => [
                    'from' => $travelUsers->firstItem(),
                    'to' => $travelUsers->lastItem(),
                    'total' => $travelUsers->total(),
                    'current_page' => $travelUsers->currentPage(),
                    'last_page' => $travelUsers->lastPage(),
                ],
                'filters' => [
                    'search' => $request->get('search'),
                    'travel_company' => $request->get('travel_company'),
                    'kabupaten' => $request->get('kabupaten'),
                    'travel_status' => $request->get('travel_status'),
                ]
            ]);
        }

        return view('admin.travel.index', compact('travelUsers', 'travelCompanies', 'kabupatens', 'travelStatuses'));
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
            'nomor_hp' => 'required|string|max:20|unique:users|regex:/^08/',
            'kabupaten' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ], [
            'nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'kabupaten' => $request->kabupaten,
            'password' => Hash::make($request->password),
            'role' => 'kabupaten',
            'country' => 'Indonesia', // Default value
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
            'nomor_hp' => 'required|string|max:20|unique:users|regex:/^08/',
            'password' => 'required|string|min:5',
            'travel_id' => 'required|exists:travels,id',
        ], [
            'nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
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
        $user = User::with('travel')->findOrFail($id);
        $currentUser = auth()->user();

        // Check if kabupaten user is trying to edit travel user from different kabupaten
        if ($currentUser->role === 'kabupaten' && $user->role === 'user') {
            if (!$user->travel || $user->travel->kab_kota !== $currentUser->kabupaten) {
                return redirect()->back()->with('error', 'Anda hanya bisa mengedit user travel dari kabupaten Anda sendiri.');
            }
        }

        // Get travel companies for dropdown - avoid N+1 by passing from controller
        $travelCompanies = collect();
        if ($currentUser->role === 'admin') {
            $travelCompanies = TravelCompany::select('id', 'Penyelenggara', 'kab_kota')->orderBy('Penyelenggara')->get();
        } else if ($currentUser->role === 'kabupaten') {
            $travelCompanies = TravelCompany::select('id', 'Penyelenggara', 'kab_kota')
                ->where('kab_kota', $currentUser->kabupaten)
                ->orderBy('Penyelenggara')->get();
        }

        return view('admin.users.edit', compact('user', 'travelCompanies'));
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

        $validationRules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'nomor_hp' => 'required|string|max:20|unique:users,nomor_hp,' . $id . '|regex:/^08/',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal' => 'nullable|string|max:10',
        ];

        // Add kabupaten validation for kabupaten users
        if ($user->role === 'kabupaten') {
            $validationRules['kabupaten'] = 'required|string|max:255';
        }

        $request->validate($validationRules, [
            'nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
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

        // Add kabupaten for kabupaten users
        if ($user->role === 'kabupaten' && $request->filled('kabupaten')) {
            $updateData['kabupaten'] = $request->kabupaten;
        }

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
        return view('admin.travel.import');
    }

    /**
     * Handle Excel import for travel users
     */
    public function importTravelUsers(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            \Log::info('ImportTravelUsers: Mulai proses import file.', [
                'filename' => $request->file('excel_file')->getClientOriginalName(),
                'size' => $request->file('excel_file')->getSize(),
            ]);

            // Create import instance
            $import = new UserTravelImport();

            \Log::info('ImportTravelUsers: Sebelum Excel::import dipanggil');

            // Import the Excel file
            Excel::import($import, $request->file('excel_file'));

            \Log::info('ImportTravelUsers: Sesudah Excel::import dipanggil');

            DB::commit();

            // Get import results
            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();

            \Log::info('ImportTravelUsers: Hasil import', [
                'successCount' => $successCount,
                'errors' => $errors,
            ]);

            $message = "Import berhasil! {$successCount} user berhasil dibuat.";

            if (!empty($errors)) {
                $message .= "\n\nError yang ditemukan:\n" . implode("\n", $errors);
            }

            return redirect()->route('travels.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('UserTravelImport error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

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
