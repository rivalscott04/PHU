<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Models\BAP;
use App\Models\BapAirline;
use App\Models\TravelPackage;
use App\Support\OperatorScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TravelPackageController extends Controller
{
    public function index()
    {
        $user = $this->authorizedTravelUser();

        $packages = TravelPackage::query()
            ->tap(fn ($q) => OperatorScope::apply($q, $user))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $priceHistoryColumns = ['id', 'datetime', 'price', 'people', 'status'];
        if (Schema::hasColumn('bap', 'package')) {
            $priceHistoryColumns[] = 'package';
        }

        $priceHistory = BAP::query()
            ->where('user_id', $user->id)
            ->whereNotNull('price')
            ->orderByDesc('datetime')
            ->limit(10)
            ->get($priceHistoryColumns);

        $airlineOptions = BapAirline::activeNames();

        return view('travel.packages.index', compact('packages', 'priceHistory', 'airlineOptions'));
    }

    public function store(Request $request)
    {
        $user = $this->authorizedTravelUser();
        $data = $this->validatedPayload($request);

        TravelPackage::create([
            ...$data,
            ...OperatorScope::ownerColumns($user),
        ]);

        return redirect()
            ->route('travel.packages')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    public function update(Request $request, TravelPackage $package)
    {
        $user = $this->authorizedTravelUser();
        $this->authorizePackage($user, $package);

        $package->update($this->validatedPayload($request));

        return redirect()
            ->route('travel.packages')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(TravelPackage $package)
    {
        $user = $this->authorizedTravelUser();
        $this->authorizePackage($user, $package);

        $package->delete();

        return redirect()
            ->route('travel.packages')
            ->with('success', 'Paket berhasil dihapus.');
    }

    private function authorizedTravelUser()
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'user' || ! $user->operatingTravelId()) {
            abort(403, 'Akses ditolak.');
        }

        $travel = $user->operatingTravel();

        if (! $travel?->isRegistrationApproved()) {
            abort(403, 'Akun travel belum diverifikasi Kanwil.');
        }

        return $user;
    }

    private function authorizePackage($user, TravelPackage $package): void
    {
        if (! OperatorScope::owns($user, $package->travel_id, $package->cabang_id)) {
            abort(403, 'Akses ditolak.');
        }
    }

    /** @return array<string, mixed> */
    private function validatedPayload(Request $request): array
    {
        ValidationHelper::validate($request, [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
            'days' => 'nullable|integer|min:1|max:365',
            'default_airline' => 'nullable|string|max:255',
            'service_notes' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ]);

        return [
            'name' => trim($request->input('name')),
            'price' => $request->input('price'),
            'days' => $request->filled('days') ? (int) $request->input('days') : null,
            'default_airline' => $request->filled('default_airline') ? trim($request->input('default_airline')) : null,
            'service_notes' => $request->filled('service_notes') ? trim($request->input('service_notes')) : null,
            'is_active' => $request->boolean('is_active', true),
        ];
    }
}
