<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;

/**
 * Travel yang belum disetujui boleh login, tetapi hanya Beranda, profil,
 * notifikasi, dan formulir perbaikan pendaftaran. Menu operasional dikunci.
 */
class EnsureRegistrationApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::User->value) {
            return $next($request);
        }

        $registration = $user->operatingRegistration();

        if (! $registration || $registration->isRegistrationApproved()) {
            return $next($request);
        }

        if ($request->routeIs(
            'home',
            'profile.show',
            'profile.update',
            'logout',
            'logout.redirect',
            'registration.revision.edit',
            'registration.revision.update',
            'user.changePassword',
            'user.updatePassword',
            'impersonate.leave',
            'v2.notifications.*',
            'v2.sidebar-badges',
        )) {
            return $next($request);
        }

        return redirect()
            ->route('home')
            ->with('error', 'Pendaftaran belum disetujui. Menu dibuka setelah verifikasi.');
    }
}
