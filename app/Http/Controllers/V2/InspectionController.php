<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Http\Requests\StoreFindingRequest;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionChecklistsRequest;
use App\Http\Requests\UpdateInspectionRequest;
use App\Models\Inspection;
use App\Models\TravelCompany;
use App\Services\InspectionService;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly InspectionService $inspectionService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Inspection::class);

        $inspections = $this->inspectionService->paginate(
            $this->scopeFilters($request, ['status', 'inspection_type', 'search', 'date_from', 'date_to'])
        );

        if ($request->expectsJson()) {
            return $this->jsonSuccess($inspections);
        }

        return view('v2.pengawasan.index', compact('inspections'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Inspection::class);

        $travels = $this->resolveTravels();
        $preselectedTravelId = $request->integer('travel_id') ?: null;

        if ($preselectedTravelId && ! $travels->contains('id', $preselectedTravelId)) {
            $preselectedTravelId = null;
        }

        return view('v2.pengawasan.form', [
            'inspection' => null,
            'travels' => $travels,
            'preselectedTravelId' => $preselectedTravelId,
        ]);
    }

    public function store(StoreInspectionRequest $request)
    {
        $this->authorize('create', Inspection::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $data['status'] = 'DRAFT';

        $inspection = $this->inspectionService->create($data);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($inspection, 'Pengawasan berhasil dibuat.', 201);
        }

        return redirect()->route('v2.pengawasan.show', $inspection)
            ->with('success', 'Pengawasan berhasil dibuat.');
    }

    public function show(Request $request, Inspection $pengawasan)
    {
        $this->authorize('view', $pengawasan);

        $inspection = $this->inspectionService->find($pengawasan->id);
        $checklistGroups = $this->groupChecklists($inspection);
        $canFillChecklist = auth()->user()->can('update', $inspection)
            && ! in_array($inspection->status?->value ?? $inspection->status, ['CLOSED', 'CANCELLED'], true);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($inspection);
        }

        return view('v2.pengawasan.show', compact('inspection', 'checklistGroups', 'canFillChecklist'));
    }

    public function edit(Inspection $pengawasan)
    {
        $this->authorize('update', $pengawasan);

        return view('v2.pengawasan.form', [
            'inspection' => $this->inspectionService->find($pengawasan->id),
            'travels' => $this->resolveTravels(),
        ]);
    }

    public function update(UpdateInspectionRequest $request, Inspection $pengawasan)
    {
        $this->authorize('update', $pengawasan);

        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        $inspection = $this->inspectionService->update($pengawasan, $data);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($inspection, 'Pengawasan berhasil diperbarui.');
        }

        return redirect()->route('v2.pengawasan.show', $inspection)
            ->with('success', 'Pengawasan berhasil diperbarui.');
    }

    public function storeFinding(StoreFindingRequest $request, Inspection $pengawasan)
    {
        $this->authorize('update', $pengawasan);

        $finding = $this->inspectionService->createFinding($pengawasan, $request->validated());

        return $request->expectsJson()
            ? $this->jsonSuccess($finding, 'Temuan berhasil ditambahkan.', 201)
            : back()->with('success', 'Temuan berhasil ditambahkan.');
    }

    public function updateChecklists(UpdateInspectionChecklistsRequest $request, Inspection $pengawasan)
    {
        $this->authorize('update', $pengawasan);

        try {
            $inspection = $this->inspectionService->updateChecklists($pengawasan, $request->validated('items'));
        } catch (\InvalidArgumentException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 422)
                : back()->withInput()->withErrors(['checklist' => $e->getMessage()]);
        }

        return $request->expectsJson()
            ? $this->jsonSuccess($inspection, 'Daftar periksa berhasil disimpan.')
            : redirect()->route('v2.pengawasan.show', $inspection)
                ->with('success', 'Daftar periksa berhasil disimpan.');
    }

    private function groupChecklists(Inspection $inspection): \Illuminate\Support\Collection
    {
        return $inspection->checklists
            ->sortBy([
                fn ($item) => $item->masterChecklist?->category?->sort_order ?? 999,
                fn ($item) => $item->masterChecklist?->sort_order ?? 999,
                fn ($item) => $item->id,
            ])
            ->groupBy(fn ($item) => $item->masterChecklist?->category?->name ?? 'Lainnya');
    }

    private function resolveTravels()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return TravelCompany::orderBy('Penyelenggara')->get();
        }

        if ($user->role === 'pengawas') {
            $scoped = $user->getScopedKabupatens();

            if ($scoped === null) {
                return TravelCompany::orderBy('Penyelenggara')->get();
            }

            return TravelCompany::whereIn('kab_kota', $scoped)->orderBy('Penyelenggara')->get();
        }

        return collect();
    }
}
