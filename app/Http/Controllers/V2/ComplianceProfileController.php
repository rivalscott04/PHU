<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Models\TravelCompany;
use App\Policies\CompliancePolicy;
use App\Services\ComplianceService;
use App\Support\RequestScope;
use App\Support\ResourceAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ComplianceProfileController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly ComplianceService $complianceService,
    ) {
    }

    public function index(Request $request)
    {
        abort_unless((new CompliancePolicy())->viewAny($request->user()), 403);

        $scope = RequestScope::fromRequest($request);

        $query = TravelCompany::query()
            ->with('riskScore')
            ->orderBy('Penyelenggara');

        if (Schema::hasTable('pengawasan')) {
            $query->withCount('inspections');
        }

        if (Schema::hasTable('pengaduan')) {
            $query->withCount('pengaduan');
        }

        if ($scope->kabupaten) {
            $query->where('kab_kota', $scope->kabupaten);
        } elseif ($scope->kabupatens) {
            $query->whereIn('kab_kota', $scope->kabupatens);
        }

        if ($scope->travelId) {
            $query->where('id', $scope->travelId);
        }

        $travels = $query->paginate(15);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($travels);
        }

        return view('v2.compliance.index', compact('travels'));
    }

    public function show(Request $request, TravelCompany $travel)
    {
        ResourceAccess::denyUnless((new CompliancePolicy())->view($request->user(), $travel));

        $profile = $this->complianceService->getProfile($travel->id);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($profile);
        }

        return view('v2.compliance.show', $profile);
    }
}
