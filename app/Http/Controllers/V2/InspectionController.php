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
use App\Support\NtbKabupatenMap;
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

        return redirect()->route('v2.pengawasan.show', ['pengawasan' => $inspection, 'step' => 2])
            ->with('success', 'Pemeriksaan berhasil dibuat. Lanjut isi pertanyaan pemeriksaan.');
    }

    public function show(Request $request, Inspection $pengawasan)
    {
        $this->authorize('view', $pengawasan);

        $inspection = $this->inspectionService->find($pengawasan->id);
        $checklistGroups = $this->groupChecklists($inspection);
        $canFillChecklist = auth()->user()->can('update', $inspection)
            && ! in_array($inspection->status?->value ?? $inspection->status, ['CLOSED', 'CANCELLED'], true);

        $checklistFilled = $inspection->checklists->filter(fn ($item) => filled($item->answer))->count();
        $checklistTotal = $inspection->checklists->count();
        $checklistComplete = $checklistTotal > 0 && $checklistFilled === $checklistTotal;
        $isLocked = in_array($inspection->status?->value ?? $inspection->status, ['CLOSED', 'CANCELLED'], true);
        $activeStep = $this->resolveWizardStep($request, $checklistComplete, $isLocked);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($inspection);
        }

        return view('v2.pengawasan.show', compact(
            'inspection',
            'checklistGroups',
            'canFillChecklist',
            'checklistComplete',
            'activeStep',
        ));
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

        try {
            $finding = $this->inspectionService->createFinding($pengawasan, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 422)
                : redirect()->route('v2.pengawasan.show', ['pengawasan' => $pengawasan, 'step' => 3])
                    ->withInput()
                    ->withErrors(['temuan' => $e->getMessage()]);
        }

        return $request->expectsJson()
            ? $this->jsonSuccess($finding, 'Temuan berhasil ditambahkan.', 201)
            : redirect()->route('v2.pengawasan.show', ['pengawasan' => $pengawasan, 'step' => 3])
                ->with('success', 'Masalah berhasil dicatat.');
    }

    public function updateChecklists(UpdateInspectionChecklistsRequest $request, Inspection $pengawasan)
    {
        $this->authorize('update', $pengawasan);

        try {
            $inspection = $this->inspectionService->updateChecklists($pengawasan, $request->validated('items'));
        } catch (\InvalidArgumentException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 422)
                : redirect()->route('v2.pengawasan.show', ['pengawasan' => $pengawasan, 'step' => 2])
                    ->withInput()
                    ->withErrors(['checklist' => $e->getMessage()]);
        }

        return $request->expectsJson()
            ? $this->jsonSuccess($inspection, 'Daftar periksa berhasil disimpan.')
            : redirect()->route('v2.pengawasan.show', ['pengawasan' => $pengawasan, 'step' => 3])
                ->with('success', 'Pertanyaan pemeriksaan berhasil disimpan. Lanjut ke langkah temuan.');
    }

    public function finalize(Request $request, Inspection $pengawasan)
    {
        $this->authorize('update', $pengawasan);

        try {
            $inspection = $this->inspectionService->finalize($pengawasan);
        } catch (\InvalidArgumentException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 422)
                : redirect()->route('v2.pengawasan.show', ['pengawasan' => $pengawasan, 'step' => 3])
                    ->withErrors(['finalize' => $e->getMessage()]);
        }

        $message = $inspection->findings->isEmpty()
            ? 'Pemeriksaan selesai. Tidak ada masalah yang perlu ditindaklanjuti.'
            : 'Pemeriksaan selesai. Travel akan menindaklanjuti masalah yang ditemukan.';

        return $request->expectsJson()
            ? $this->jsonSuccess($inspection, $message)
            : redirect()->route('v2.pengawasan.show', ['pengawasan' => $inspection, 'step' => 3])
                ->with('success', $message);
    }

    private function resolveWizardStep(Request $request, bool $checklistComplete, bool $isLocked): int
    {
        $step = (int) $request->query('step', 0);

        if ($step >= 1 && $step <= 3) {
            if ($step === 3 && ! $checklistComplete && ! $isLocked) {
                return 2;
            }

            return $step;
        }

        return ($checklistComplete || $isLocked) ? 3 : 2;
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

            return TravelCompany::whereIn(
                'kab_kota',
                NtbKabupatenMap::expandKabupatenList($scoped)
            )->orderBy('Penyelenggara')->get();
        }

        return collect();
    }
}
