<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Models\SupervisionWorkQueue;
use App\Policies\WorkQueuePolicy;
use App\Services\WorkQueueService;
use App\Support\RequestScope;
use App\Support\ResourceAccess;
use Illuminate\Http\Request;

class WorkQueueController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly WorkQueueService $workQueueService,
    ) {
    }

    public function index(Request $request)
    {
        abort_unless((new WorkQueuePolicy())->viewAny($request->user()), 403);

        $scope = RequestScope::fromRequest($request);
        $scopeFilters = $scope->toFilterArray();
        $this->workQueueService->syncOverdueFindings($scope->kabupaten);

        $filters = array_merge($this->scopeFilters($request, ['type', 'status', 'kabupaten']), $scopeFilters);
        $cards = $this->workQueueService->getKpiCards($scopeFilters);
        $items = $this->workQueueService->paginate(
            $request->user(),
            $filters,
            (int) $request->get('per_page', 15)
        );

        if ($request->expectsJson()) {
            return $this->jsonSuccess([
                'summary' => $this->workQueueService->getSummary($scopeFilters),
                'items' => $items,
            ]);
        }

        return view('v2.antrian.index', compact('cards', 'items', 'filters'));
    }

    public function start(Request $request, SupervisionWorkQueue $antrian)
    {
        ResourceAccess::denyUnless((new WorkQueuePolicy())->update($request->user(), $antrian));

        $this->workQueueService->markInProgress($antrian, $request->user());

        return redirect()
            ->route('v2.antrian.index', $request->only(['type', 'status']))
            ->with('success', 'Antrian ditandai sedang diproses.');
    }

    public function resolve(Request $request, SupervisionWorkQueue $antrian)
    {
        ResourceAccess::denyUnless((new WorkQueuePolicy())->update($request->user(), $antrian));

        $this->workQueueService->resolve($antrian, $request->user());

        return redirect()
            ->route('v2.antrian.index', $request->only(['type', 'status']))
            ->with('success', 'Antrian diselesaikan.');
    }
}
