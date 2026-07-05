<?php

namespace App\Http\Controllers\V2;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Policies\MonitoringPolicy;
use App\Services\DashboardService;
use App\Support\DashboardFilter;
use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {
    }

    public function index(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $filter = DashboardFilter::fromRequest($request);
        $overview = $this->dashboardService->getOverview($filter);

        if ($request->user()->role === UserRole::Pimpinan->value) {
            $overview['executive'] = $this->dashboardService->getExecutive($filter);
        }

        if ($request->expectsJson()) {
            return $this->jsonSuccess($overview);
        }

        return view('v2.dashboard.index', $overview);
    }

    public function statistics(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $filter = DashboardFilter::fromRequest($request);

        return $this->jsonSuccess($this->dashboardService->getStatistics($filter));
    }

    public function charts(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $filter = DashboardFilter::fromRequest($request);

        return $this->jsonSuccess($this->dashboardService->getCharts($filter));
    }

    public function ranking(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $filter = DashboardFilter::fromRequest($request);

        return $this->jsonSuccess($this->dashboardService->getRankings($filter));
    }

    public function timeline(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $filter = DashboardFilter::fromRequest($request);

        return $this->jsonSuccess($this->dashboardService->getTimeline($filter));
    }

    public function warning(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $filter = DashboardFilter::fromRequest($request);

        return $this->jsonSuccess($this->dashboardService->getWarnings($filter));
    }

    public function heatmap(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);

        $filter = DashboardFilter::fromRequest($request);

        return $this->jsonSuccess($this->dashboardService->getHeatmap($filter));
    }

    public function executive(Request $request)
    {
        abort_unless((new MonitoringPolicy())->view($request->user()), 403);
        abort_unless($request->user()->role === UserRole::Pimpinan->value, 403);

        $filter = DashboardFilter::fromRequest($request);

        return $this->jsonSuccess($this->dashboardService->getExecutive($filter));
    }
}
