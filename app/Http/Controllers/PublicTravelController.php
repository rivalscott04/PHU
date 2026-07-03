<?php

namespace App\Http\Controllers;

use App\Models\CabangTravel;
use App\Models\TravelCompany;
use App\Services\PublicTravelProfileService;
use App\Support\PublicTrustIndex;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicTravelController extends Controller
{
    public function __construct(
        private readonly PublicTravelProfileService $profileService,
    ) {
    }

    public function index(Request $request)
    {
        $travelPusat = TravelCompany::query()
            ->select(
                'id',
                'Penyelenggara',
                'kab_kota',
                'Status',
                'Pimpinan',
                'telepon',
                'Telepon',
                'alamat_kantor_baru',
                'alamat_kantor_lama',
                'nilai_akreditasi',
                'Tanggal',
                'license_expiry',
            )
            ->with('riskScore')
            ->get()
            ->map(function ($item) {
                $item->type = 'pusat';
                $item->trust = PublicTrustIndex::fromRiskScore($item->riskScore);
                if ($item->Tanggal && is_string($item->Tanggal)) {
                    $item->Tanggal = \Carbon\Carbon::parse($item->Tanggal);
                }

                return $item;
            });

        $pusatByName = $travelPusat->keyBy('Penyelenggara');

        $travelCabang = CabangTravel::query()
            ->select('id_cabang', 'Penyelenggara', 'kabupaten', 'pusat', 'pimpinan_cabang', 'telepon', 'alamat_cabang', 'SK_BA', 'tanggal')
            ->get()
            ->map(function ($item) use ($pusatByName) {
                $item->type = 'cabang';
                $item->id = $item->id_cabang;
                if ($item->tanggal && is_string($item->tanggal)) {
                    $item->tanggal = \Carbon\Carbon::parse($item->tanggal);
                }

                $parent = $pusatByName->get($item->pusat);
                $item->parent_travel_id = $parent?->id;
                $item->trust = $parent
                    ? PublicTrustIndex::fromRiskScore($parent->riskScore)
                    : PublicTrustIndex::empty();

                return $item;
            });

        $allKabupatens = $travelPusat->pluck('kab_kota')
            ->merge($travelCabang->pluck('kabupaten'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $stats = [
            'total' => $travelPusat->count() + $travelCabang->count(),
            'ppiu' => $travelPusat->where('Status', 'PPIU')->count(),
            'pihk' => $travelPusat->where('Status', 'PIHK')->count(),
            'pusat' => $travelPusat->count(),
            'cabang' => $travelCabang->count(),
            'kabupaten' => $allKabupatens->count(),
            'with_trust_data' => $travelPusat->filter(fn ($t) => $t->trust['has_data'])->count(),
        ];

        $allTravels = $travelPusat->concat($travelCabang);
        $perPage = 6;
        $currentPage = (int) $request->get('page', 1);
        $total = $allTravels->count();
        $items = $allTravels->forPage($currentPage, $perPage);

        $pagination = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        return view('travel-list-public', [
            'data' => $pagination->items(),
            'pagination' => $pagination,
            'totalCount' => $total,
            'allKabupatens' => $allKabupatens,
            'stats' => $stats,
        ]);
    }

    public function show(TravelCompany $travel)
    {
        $travel->loadMissing('riskScore');
        $profile = $this->profileService->getProfile($travel);

        if ($profile === []) {
            abort(404);
        }

        return view('travel-profile-public', $profile);
    }
}
