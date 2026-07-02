<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Repositories\RiskRepository;
use App\Services\RiskCalculationService;
use App\Support\TravelAccess;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly RiskRepository $riskRepository,
        private readonly RiskCalculationService $riskCalculationService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', RiskScore::class);

        $filters = $this->scopeFilters($request, ['risk_level']);
        $risks = $this->riskRepository->paginate($filters);
        $levelCounts = $this->riskRepository->countByRiskLevel($filters);
        $cards = $this->buildRiskCards($levelCounts);

        if ($request->expectsJson()) {
            return $this->jsonSuccess([
                'risks' => $risks,
                'summary' => $cards,
            ]);
        }

        return view('v2.risk.index', compact('risks', 'cards', 'filters'));
    }

    public function show(Request $request, TravelCompany $travel)
    {
        TravelAccess::authorize($request, $travel);

        $risk = $this->riskRepository->findByTravelId($travel->id);
        $breakdown = $this->riskCalculationService->getBreakdown($travel->id);

        if ($risk) {
            $this->authorize('view', $risk);
        } else {
            $this->authorize('viewAny', RiskScore::class);
        }

        if ($request->expectsJson()) {
            return $this->jsonSuccess([
                'risk' => $risk,
                'breakdown' => $breakdown,
            ]);
        }

        return view('v2.risk.show', compact('travel', 'risk', 'breakdown'));
    }

    public function recalculate(Request $request)
    {
        abort_unless((new \App\Policies\RiskPolicy())->recalculate($request->user()), 403);

        $count = $this->riskCalculationService->recalculateAll();

        return $request->expectsJson()
            ? $this->jsonSuccess(['recalculated' => $count], "Risk dihitung ulang untuk {$count} travel.")
            : back()->with('success', "Risk dihitung ulang untuk {$count} travel.");
    }

    public function recalculateTravel(Request $request, TravelCompany $travel)
    {
        abort_unless((new \App\Policies\RiskPolicy())->recalculate($request->user()), 403);

        $risk = $this->riskCalculationService->recalculateForTravel($travel->id);

        return $request->expectsJson()
            ? $this->jsonSuccess($risk, 'Risk travel dihitung ulang.')
            : back()->with('success', 'Risk travel dihitung ulang.');
    }

    /** @return array<string, array{label: string, value: int, icon: string, color: string}> */
    private function buildRiskCards(array $levelCounts): array
    {
        return [
            'critical' => [
                'label' => 'Kritis',
                'value' => (int) ($levelCounts['CRITICAL'] ?? 0),
                'icon' => 'bx-error',
                'color' => '#f46a6a',
            ],
            'high' => [
                'label' => 'Tinggi',
                'value' => (int) ($levelCounts['HIGH'] ?? 0),
                'icon' => 'bx-error-circle',
                'color' => '#f1b44c',
            ],
            'medium' => [
                'label' => 'Sedang',
                'value' => (int) ($levelCounts['MEDIUM'] ?? 0),
                'icon' => 'bx-info-circle',
                'color' => '#50a5f1',
            ],
            'low' => [
                'label' => 'Rendah',
                'value' => (int) ($levelCounts['LOW'] ?? 0),
                'icon' => 'bx-check-shield',
                'color' => '#34c38f',
            ],
        ];
    }
}
