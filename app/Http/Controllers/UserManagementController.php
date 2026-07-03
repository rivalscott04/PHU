<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\PengawasScopeMode;
use App\Support\NtbKabupatenMap;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TravelCompany;
use App\Imports\UserTravelImport;
use App\Imports\UserCabangImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserManagementController extends Controller
{
    /**
     * Unified user listing for super admin (tabbed by role).
     */
    public function index(Request $request)
    {
        $activeTab = $this->resolveUserTab($request->get('tab'));
        $query = $this->buildManagedUserQuery($request, $activeTab);

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['nama', 'email', 'nomor_hp', 'kabupaten', 'created_at'];

        if (! in_array($sortBy, $allowedSortFields, true)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');

        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $users = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tableBody' => view('admin.users.partials.table-body', compact('users', 'activeTab'))->render(),
                'pagination' => view('admin.users.partials.pagination', compact('users'))->render(),
                'pagination_info' => [
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                    'total' => $users->total(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                ],
                'filters' => [
                    'tab' => $activeTab,
                    'search' => $request->get('search'),
                    'kabupaten' => $request->get('kabupaten'),
                    'travel_company' => $request->get('travel_company'),
                ],
            ]);
        }

        $kabupatens = TravelCompany::select('kab_kota')->distinct()->orderBy('kab_kota')->pluck('kab_kota');
        $travelCompanies = TravelCompany::select('Penyelenggara')->distinct()->orderBy('Penyelenggara')->pluck('Penyelenggara');
        $tabCounts = $this->managedUserTabCounts();

        return view('admin.users.index', compact(
            'users',
            'activeTab',
            'kabupatens',
            'travelCompanies',
            'tabCounts',
        ));
    }

    private function resolveUserTab(?string $tab): string
    {
        $allowed = [
            UserRole::Pimpinan->value,
            UserRole::Kabupaten->value,
            UserRole::Pengawas->value,
            UserRole::User->value,
        ];

        return in_array($tab, $allowed, true) ? $tab : UserRole::Pimpinan->value;
    }

    /** @return array<string, int> */
    private function managedUserTabCounts(): array
    {
        return [
            UserRole::Pimpinan->value => User::where('role', UserRole::Pimpinan->value)->count(),
            UserRole::Kabupaten->value => User::where('role', UserRole::Kabupaten->value)->count(),
            UserRole::Pengawas->value => User::where('role', UserRole::Pengawas->value)->count(),
            UserRole::User->value => User::where('role', UserRole::User->value)->count(),
        ];
    }

    private function buildManagedUserQuery(Request $request, string $activeTab)
    {
        $query = User::query()->with('travel')->where('role', $activeTab);

        if ($request->filled('kabupaten')) {
            if ($activeTab === UserRole::User->value) {
                $query->whereHas('travel', fn ($travelQuery) => $travelQuery->where('kab_kota', $request->kabupaten));
            } else {
                $query->where('kabupaten', $request->kabupaten);
            }
        }

        if ($activeTab === UserRole::User->value && $request->filled('travel_company')) {
            $query->whereHas('travel', fn ($travelQuery) => $travelQuery->where('Penyelenggara', $request->travel_company));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $activeTab) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nomor_hp', 'like', "%{$search}%");

                if ($activeTab !== UserRole::User->value && $activeTab !== UserRole::Pimpinan->value) {
                    $q->orWhere('kabupaten', 'like', "%{$search}%");
                }

                if ($activeTab === UserRole::User->value) {
                    $q->orWhereHas('travel', function ($travelQuery) use ($search) {
                        $travelQuery->where('Penyelenggara', 'like', "%{$search}%")
                            ->orWhere('kab_kota', 'like', "%{$search}%");
                    });
                }
            });
        }

        return $query;
    }

    /**
     * Show unified create form with role assignment.
     */
    public function create()
    {
        return view('admin.users.create', $this->userFormViewData([
            'roleOptions' => UserRole::assignableByAdmin(),
            'travelCompanies' => TravelCompany::orderBy('Penyelenggara')->get(['id', 'Penyelenggara', 'kab_kota']),
        ]));
    }

    /**
     * Store a user with assigned role and domain.
     */
    public function store(Request $request)
    {
        $assignableRoles = array_map(fn (UserRole $role) => $role->value, UserRole::assignableByAdmin());

        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nomor_hp' => 'required|string|max:20|unique:users|regex:/^08/',
            'password' => 'required|string|min:8',
            'role' => 'required|in:'.implode(',', $assignableRoles),
        ];

        $role = UserRole::from($request->input('role'));

        if ($role === UserRole::Kabupaten) {
            $rules['kabupaten'] = 'required|string|max:255';
        }

        if ($role === UserRole::Pengawas) {
            $rules = array_merge($rules, $this->pengawasScopeRules($request));
        }

        if ($role->requiresTravel()) {
            $rules['travel_id'] = 'required|exists:travels,id';
        }

        $validated = $request->validate($rules, [
            'nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
        ]);

        $payload = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'nomor_hp' => $validated['nomor_hp'],
            'password' => Hash::make($validated['password']),
            'role' => $role->value,
            'country' => 'Indonesia',
            'is_password_changed' => false,
            'travel_id' => null,
            'kabupaten' => null,
            'pengawas_scope' => null,
            'pengawas_kabupatens' => null,
        ];

        if ($role === UserRole::Pengawas) {
            $payload = array_merge($payload, $this->pengawasPayloadFromRequest($request));
        } elseif ($role === UserRole::Kabupaten) {
            $payload['kabupaten'] = $validated['kabupaten'];
        }

        if ($role->requiresTravel()) {
            $travel = TravelCompany::findOrFail($validated['travel_id']);
            $payload['travel_id'] = $travel->id;
            $payload['kabupaten'] = $travel->kab_kota;
        }

        User::create($payload);

        return redirect()
            ->route('users.index', ['tab' => $role->value])
            ->with('success', 'Pengguna '.$role->label().' berhasil ditambahkan.');
    }

    /**
     * Display a listing of kabupaten users
     */
    public function indexKabupaten(Request $request)
    {
        if (! $request->ajax()) {
            return redirect()->route('users.index', array_merge(
                ['tab' => UserRole::Kabupaten->value],
                $request->query()
            ));
        }

        // Legacy AJAX fallback
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
        if (! $request->ajax()) {
            return redirect()->route('users.index', array_merge(
                ['tab' => UserRole::User->value],
                $request->query()
            ));
        }

        // Legacy AJAX fallback
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

        $roleOptions = UserRole::assignableByAdmin();
        $kabupatens = TravelCompany::select('kab_kota')->distinct()->orderBy('kab_kota')->pluck('kab_kota');

        return view('admin.users.edit', $this->userFormViewData([
            'user' => $user,
            'travelCompanies' => $travelCompanies,
            'roleOptions' => $roleOptions,
            'isPengawas' => $user->role === UserRole::Pengawas->value,
            'showPengawasScope' => $user->role === UserRole::Pengawas->value,
            'pengawasScope' => $user->pengawas_scope ?? PengawasScopeMode::Single->value,
            'pengawasKabupatens' => $user->pengawas_kabupatens ?? [],
            'singleKabupaten' => $user->kabupaten,
        ]));
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

        // Add kabupaten validation for scoped roles
        $role = UserRole::tryFromString($user->role);
        if ($role === UserRole::Kabupaten) {
            $validationRules['kabupaten'] = 'required|string|max:255';
        }

        if ($role === UserRole::Pengawas) {
            $validationRules = array_merge($validationRules, $this->pengawasScopeRules($request));
        }

        if ($user->role === UserRole::User->value) {
            $validationRules['travel_id'] = 'required|exists:travels,id';
        }

        $validationRules['password'] = 'nullable|string|min:8';

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

        if ($role === UserRole::Kabupaten && $request->filled('kabupaten')) {
            $updateData['kabupaten'] = $request->kabupaten;
        }

        if ($role === UserRole::Pengawas) {
            $updateData = array_merge($updateData, $this->pengawasPayloadFromRequest($request));
        }

        if ($user->role === UserRole::User->value) {
            $updateData['travel_id'] = $request->travel_id;
        }

        $user->update($updateData);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
                'is_password_changed' => 0,
            ]);
        }

        return redirect()
            ->route('users.index', ['tab' => $user->role === UserRole::Admin->value ? UserRole::Kabupaten->value : $user->role])
            ->with('success', 'User berhasil diupdate!');
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

        if ($user->role === UserRole::Admin->value) {
            return redirect()->back()->with('error', 'Akun super admin tidak dapat dihapus.');
        }

        $deletedRole = $user->role;
        $user->delete();

        return redirect()
            ->route('users.index', ['tab' => $deletedRole])
            ->with('success', 'User berhasil dihapus!');
    }

    /**
     * Show the form for importing travel users via Excel
     */
    public function importTravelForm()
    {
        return view('admin.travel.import');
    }

    public function importCabangForm()
    {
        return view('admin.travel.import-cabang');
    }

    /**
     * Handle Excel import for travel users (PUSAT)
     */
    public function importTravelUsers(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            \Log::info('ImportTravelUsers (PUSAT): Mulai proses import file.', [
                'filename' => $request->file('excel_file')->getClientOriginalName(),
                'size' => $request->file('excel_file')->getSize(),
            ]);

            // Create import instance for PUSAT
            $import = new UserTravelImport();

            \Log::info('ImportTravelUsers (PUSAT): Sebelum Excel::import dipanggil');

            // Import the Excel file
            Excel::import($import, $request->file('excel_file'));

            \Log::info('ImportTravelUsers (PUSAT): Sesudah Excel::import dipanggil');

            DB::commit();

            // Get import results
            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();

            \Log::info('ImportTravelUsers (PUSAT): Hasil import', [
                'successCount' => $successCount,
                'errors' => $errors,
            ]);

            // Generate user-friendly message
            if ($successCount > 0 && empty($errors)) {
                $message = "✅ Import berhasil! {$successCount} user pusat berhasil ditambahkan.";
                $messageType = 'success';
            } elseif ($successCount > 0 && !empty($errors)) {
                $errorCount = count($errors);
                $message = "⚠️ Import sebagian berhasil! {$successCount} user berhasil ditambahkan, {$errorCount} data bermasalah.";
                $messageType = 'warning';
            } elseif ($successCount == 0 && !empty($errors)) {
                $errorCount = count($errors);
                $message = "❌ Import gagal! {$errorCount} data bermasalah. Silakan periksa data dan coba lagi.";
                $messageType = 'error';
            } else {
                $message = "❌ Import gagal! Tidak ada data yang dapat diproses.";
                $messageType = 'error';
            }

            // Store detailed errors in session for admin view (optional)
            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
            }

            return redirect()->route('travels.index')
                ->with($messageType, $message);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('UserTravelImport (PUSAT) error', [
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
     * Handle Excel import for cabang travel users (CABANG)
     */
    public function importCabangUsers(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            \Log::info('ImportCabangUsers (CABANG): Mulai proses import file.', [
                'filename' => $request->file('excel_file')->getClientOriginalName(),
                'size' => $request->file('excel_file')->getSize(),
            ]);

            // Create import instance for CABANG
            $import = new UserCabangImport();

            \Log::info('ImportCabangUsers (CABANG): Sebelum Excel::import dipanggil');

            // Import the Excel file
            Excel::import($import, $request->file('excel_file'));

            \Log::info('ImportCabangUsers (CABANG): Sesudah Excel::import dipanggil');

            DB::commit();

            // Get import results
            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();

            \Log::info('ImportCabangUsers (CABANG): Hasil import', [
                'successCount' => $successCount,
                'errors' => $errors,
            ]);

            // Generate user-friendly message
            if ($successCount > 0 && empty($errors)) {
                $message = "✅ Import berhasil! {$successCount} user cabang berhasil ditambahkan.";
                $messageType = 'success';
            } elseif ($successCount > 0 && !empty($errors)) {
                $errorCount = count($errors);
                $message = "⚠️ Import sebagian berhasil! {$successCount} user berhasil ditambahkan, {$errorCount} data bermasalah.";
                $messageType = 'warning';
            } elseif ($successCount == 0 && !empty($errors)) {
                $errorCount = count($errors);
                $message = "❌ Import gagal! {$errorCount} data bermasalah. Silakan periksa data dan coba lagi.";
                $messageType = 'error';
            } else {
                $message = "❌ Import gagal! Tidak ada data yang dapat diproses.";
                $messageType = 'error';
            }

            // Store detailed errors in session for admin view (optional)
            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
            }

            return redirect()->route('travels.index')
                ->with($messageType, $message);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('UserCabangImport (CABANG) error', [
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
     * Download Excel template for travel users import (PUSAT)
     */
    public function downloadTravelUserTemplate()
    {
        $filePath = public_path('template/templateuser.xlsx');

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Template file tidak ditemukan.');
        }

        return response()->download($filePath, 'Template_Import_User_Travel_PUSAT.xlsx');
    }

    /**
     * Download Excel template for cabang users import (CABANG)
     */
    public function downloadCabangUserTemplate()
    {
        $filePath = public_path('template/templateuser.xlsx');

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Template file tidak ditemukan.');
        }

        return response()->download($filePath, 'Template_Import_User_Travel_CABANG.xlsx');
    }

    /** @return array<string, mixed> */
    private function userFormViewData(array $extra = []): array
    {
        return array_merge([
            'kabupatens' => $this->kabupatenOptions(),
            'pengawasScopeModes' => PengawasScopeMode::options(),
        ], $extra);
    }

  /** @return \Illuminate\Support\Collection<int, string> */
    private function kabupatenOptions()
    {
        $fromTravels = TravelCompany::select('kab_kota')->distinct()->orderBy('kab_kota')->pluck('kab_kota');
        $fromMap = collect(array_keys(NtbKabupatenMap::centroids()));

        return $fromMap->merge($fromTravels)->unique()->sort()->values();
    }

    /** @return array<string, string|array<int, string>> */
    private function pengawasScopeRules(Request $request): array
    {
        $rules = [
            'pengawas_scope' => 'required|in:'.implode(',', array_map(
                fn (PengawasScopeMode $mode) => $mode->value,
                PengawasScopeMode::options()
            )),
        ];

        $mode = PengawasScopeMode::tryFrom((string) $request->input('pengawas_scope'));

        if ($mode === PengawasScopeMode::Single) {
            $rules['kabupaten'] = 'required|string|max:255';
        }

        if ($mode === PengawasScopeMode::Custom) {
            $rules['pengawas_kabupatens'] = 'required|array|min:1';
            $rules['pengawas_kabupatens.*'] = 'required|string|max:255';
        }

        return $rules;
    }

    /** @return array<string, mixed> */
    private function pengawasPayloadFromRequest(Request $request): array
    {
        $mode = PengawasScopeMode::from((string) $request->input('pengawas_scope'));

        $payload = [
            'pengawas_scope' => $mode->value,
            'pengawas_kabupatens' => null,
            'kabupaten' => null,
        ];

        return match ($mode) {
            PengawasScopeMode::All => $payload,
            PengawasScopeMode::Single => array_merge($payload, [
                'kabupaten' => $request->input('kabupaten'),
            ]),
            PengawasScopeMode::Custom => array_merge($payload, [
                'pengawas_kabupatens' => array_values(array_unique($request->input('pengawas_kabupatens', []))),
                'kabupaten' => $request->input('pengawas_kabupatens')[0] ?? null,
            ]),
        };
    }
}
