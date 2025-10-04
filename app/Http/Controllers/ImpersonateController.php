<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Lab404\Impersonate\Services\ImpersonateManager;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    protected $impersonateManager;

    public function __construct(ImpersonateManager $impersonateManager)
    {
        $this->impersonateManager = $impersonateManager;
    }

    /**
     * Show list of users that can be impersonated
     */
    public function index()
    {
        $users = User::whereIn('role', ['user', 'kabupaten'])->get();
        return view('impersonate.index', compact('users'));
    }

    /**
     * Start impersonating a user
     */
    public function impersonate($id)
    {
        $user = User::findOrFail($id);
        
        if (!$user->canBeImpersonated()) {
            return redirect()->back()->with('error', 'User cannot be impersonated.');
        }

        if (!Auth::user()->canImpersonate()) {
            return redirect()->back()->with('error', 'You do not have permission to impersonate users.');
        }

        $this->impersonateManager->take(Auth::user(), $user);

        return redirect()->route('home')->with('success', 'Now impersonating ' . $user->nama);
    }

    /**
     * Stop impersonating
     */
    public function leave()
    {
        $this->impersonateManager->leave();

        return redirect()->route('home')->with('success', 'Impersonation ended.');
    }
} 