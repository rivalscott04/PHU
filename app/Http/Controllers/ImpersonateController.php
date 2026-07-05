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
        $users = User::query()
            ->with(['travel', 'cabang'])
            ->whereIn('role', User::impersonatableRoles())
            ->orderBy('role')
            ->orderBy('nama')
            ->get();

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

        if (! $this->impersonateManager->take(Auth::user(), $user)) {
            return redirect()->back()->with('error', 'Gagal memulai impersonasi. Silakan coba lagi.');
        }

        return redirect()
            ->route($user->impersonationRedirectRoute())
            ->with('success', 'Sekarang masuk sebagai '.$user->nama);
    }

    /**
     * Stop impersonating
     */
    public function leave()
    {
        $wasAdmin = $this->impersonateManager->getImpersonator()?->isAdmin() ?? false;

        $this->impersonateManager->leave();

        return redirect()
            ->route($wasAdmin ? 'users.index' : 'home')
            ->with('success', 'Impersonasi diakhiri.');
    }
} 