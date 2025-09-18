<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * Show user profile page
     */
    public function show()
    {
        $user = Auth::user();
        return view('pages.user-profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nomor_hp' => 'required|string|max:20|unique:users,nomor_hp,' . $user->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal' => 'nullable|string|max:10',
            'about' => 'nullable|string',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:5',
            'new_password_confirmation' => 'nullable|string|same:new_password',
        ]);

        // Update basic profile data
        $updateData = [
            'nama' => $request->nama,
            'email' => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal' => $request->postal,
            'about' => $request->about,
        ];

        // Handle password change if provided
        if ($request->filled('new_password')) {
            // Verify current password if provided
            if ($request->filled('current_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->back()->with('error', 'Password saat ini tidak benar.');
                }
            }
            
            $updateData['password'] = Hash::make($request->new_password);
            $updateData['is_password_changed'] = true;
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Profile berhasil diperbarui!');
    }
}
