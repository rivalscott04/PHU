<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Models\TravelCompany;
use App\Policies\MonitoringPolicy;
use App\Repositories\DashboardRepository;
use App\Services\MonitoringService;
use App\Support\RequestScope;
use App\Support\ResourceAccess;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly MonitoringService $monitoringService,
        private readonly DashboardRepository $dashboardRepository,
    ) {
    }

    /** @return array<string, mixed> */
    private function filterOptions(Request $request): array
    {
        $scope = RequestScope::fromRequest($request);

        return [
            'kabupaten' => $scope->kabupaten,
            'jenis_travel' => $request->get('jenis_travel'),
            'risk_level' => $request->get('risk_level'),
            'sort' => $request->get('sort'),
            'kabupaten_options' => $this->dashboardRepository->getKabupatenOptions(),
        ];
    }

    public function index(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $scope = RequestScope::fromRequest($request);
        $filters = $this->filterOptions($request);
        $kpiLayout = $this->monitoringService->getKpiCards(
            $scope->kabupaten,
            $scope->travelId,
            $request->user()->role,
            $request->query(),
        );
        $travels = $this->monitoringService->getTravelList(
            $scope->kabupaten,
            8,
            $scope->travelId,
            $filters['jenis_travel'],
            $filters['risk_level'],
            $filters['sort'],
        );

        if ($request->expectsJson()) {
            return $this->jsonSuccess($this->monitoringService->getKpiSummary($scope->kabupaten, $scope->travelId));
        }

        return view('v2.monitoring.index', compact('kpiLayout', 'travels', 'filters'));
    }

    public function statistics(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $scope = RequestScope::fromRequest($request);

        return $this->jsonSuccess(
            $this->monitoringService->getKpiSummary($scope->kabupaten, $scope->travelId)
        );
    }

    public function travel(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $scope = RequestScope::fromRequest($request);
        $filters = $this->filterOptions($request);
        $travels = $this->monitoringService->getTravelList(
            $scope->kabupaten,
            (int) $request->get('per_page', 15),
            $scope->travelId,
            $filters['jenis_travel'],
            $filters['risk_level'],
            $filters['sort'],
        );

        return $request->expectsJson()
            ? $this->jsonSuccess($travels)
            : view('v2.monitoring.travel', compact('travels', 'filters'));
    }

    public function travelPengaduan(Request $request, TravelCompany $travel)
    {
        ResourceAccess::denyUnless(
            (new MonitoringPolicy())->viewTravelPengaduan($request->user(), $travel)
        );

        $items = $this->monitoringService->getTravelPengaduanList($travel);

        return $this->jsonSuccess([
            'travel' => [
                'id' => $travel->id,
                'name' => $travel->Penyelenggara,
                'kabupaten' => $travel->kab_kota,
            ],
            'pengaduan' => $items,
            'total' => count($items),
        ]);
    }

    public function kabupatenPengaduan(Request $request, string $kabupaten)
    {
        ResourceAccess::denyUnless(
            (new MonitoringPolicy())->viewKabupatenPengaduan($request->user(), $kabupaten)
        );

        $items = $this->monitoringService->getKabupatenPengaduanList($kabupaten);

        return $this->jsonSuccess([
            'kabupaten' => $kabupaten,
            'pengaduan' => $items,
            'total' => count($items),
        ]);
    }
}
