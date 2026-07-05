<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Models\TravelCompany;
use App\Policies\MonitoringPolicy;
use App\Services\MonitoringService;
use App\Support\RequestScope;
use App\Support\ResourceAccess;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly MonitoringService $monitoringService,
    ) {
    }

    public function index(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $scope = RequestScope::fromRequest($request);
        $cards = $this->monitoringService->getKpiCards($scope->kabupaten, $scope->travelId);
        $travels = $this->monitoringService->getTravelList($scope->kabupaten, 8, $scope->travelId);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($this->monitoringService->getKpiSummary($scope->kabupaten, $scope->travelId));
        }

        return view('v2.monitoring.index', compact('cards', 'travels'));
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
        $travels = $this->monitoringService->getTravelList(
            $scope->kabupaten,
            (int) $request->get('per_page', 15),
            $scope->travelId
        );

        return $request->expectsJson()
            ? $this->jsonSuccess($travels)
            : view('v2.monitoring.travel', compact('travels'));
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
}
