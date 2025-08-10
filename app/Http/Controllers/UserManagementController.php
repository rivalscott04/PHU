<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
            'username' => 'required|string|max:255|unique:users',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal' => 'required|string|max:10',
        ]);

        User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
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
            'username' => 'required|string|max:255|unique:users',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'travel_id' => 'required|exists:travels,id',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal' => 'required|string|max:10',
        ]);

        // Check if kabupaten user is trying to create travel user for different kabupaten
        if ($user->role === 'kabupaten') {
            $travelCompany = \App\Models\TravelCompany::find($request->travel_id);
            if ($travelCompany->kab_kota !== $user->kabupaten) {
                return redirect()->back()->with('error', 'Anda hanya bisa membuat user travel untuk kabupaten Anda sendiri.');
            }
        }

        User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'travel_id' => $request->travel_id,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal' => $request->postal,
            'is_password_changed' => 0,
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
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal' => 'required|string|max:10',
        ]);

        $updateData = [
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
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
}
