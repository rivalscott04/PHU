<?php

namespace App\Http\Controllers\V2;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Policies\MonitoringPolicy;
use App\Services\DashboardService;
use App\Support\DashboardFilter;
use App\Support\HomeCommandCenter;
use App\Support\RouteAccess;
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
            $queues = HomeCommandCenter::pimpinanQueues($filter->kabupaten);
            $overview['trafficLight'] = HomeCommandCenter::overallStatus($queues);
            $overview['actionQueues'] = $queues;
            $overview['alerts'] = HomeCommandCenter::pimpinanAlerts($filter->kabupaten);
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
        $data = $this->dashboardService->getExecutive($filter);
        $queues = self::pimpinanQueuesWithUrls($request, $filter->kabupaten);

        $data['command_center'] = [
            'trafficLight' => HomeCommandCenter::overallStatus($queues),
            'actionQueues' => $queues,
            'alerts' => HomeCommandCenter::pimpinanAlerts($filter->kabupaten),
        ];

        return $this->jsonSuccess($data);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function pimpinanQueuesWithUrls(Request $request, ?string $kabupaten): array
    {
        return array_map(function (array $queue) use ($request): array {
            $queue['url'] = RouteAccess::canAccessRoute(
                $request->user(),
                $queue['route'],
                $queue['params'] ?? [],
            )
                ? route($queue['route'], $queue['params'] ?? [])
                : null;

            return $queue;
        }, HomeCommandCenter::pimpinanQueues($kabupaten));
    }
}
