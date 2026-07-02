<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Policies\ExportPolicy;
use App\Services\ExportService;
use App\Support\DashboardFilter;
use App\Support\RequestScope;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly ExportService $exportService,
    ) {
    }

    public function travel(Request $request)
    {
        abort_unless((new ExportPolicy())->export($request->user()), 403);

        $scope = RequestScope::fromRequest($request);
        $format = $request->get('format', 'xlsx');

        return $this->exportService->exportTravel($scope->kabupaten, $scope->travelId, $format);
    }

    public function monitoring(Request $request)
    {
        abort_unless((new ExportPolicy())->export($request->user()), 403);

        $scope = RequestScope::fromRequest($request);
        $format = $request->get('format', 'xlsx');

        return $this->exportService->exportMonitoring($scope->kabupaten, $scope->travelId, $format);
    }

    public function pengawasan(Request $request)
    {
        abort_unless((new ExportPolicy())->exportPengawasan($request->user()), 403);

        $filters = $this->scopeFilters($request, ['status', 'inspection_type', 'search', 'date_from', 'date_to']);

        return $this->exportService->exportPengawasan($filters);
    }

    public function dashboard(Request $request)
    {
        abort_unless((new ExportPolicy())->exportDashboard($request->user()), 403);

        $filter = DashboardFilter::fromRequest($request);

        return $this->exportService->exportDashboard($filter);
    }
}
