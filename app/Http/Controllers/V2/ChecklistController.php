<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Http\Requests\StoreChecklistRequest;
use App\Http\Requests\UpdateChecklistRequest;
use App\Models\Checklist;
use App\Services\ChecklistService;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly ChecklistService $checklistService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Checklist::class);

        $checklists = $this->checklistService->paginate(
            $request->only(['category_id', 'is_active', 'search'])
        );

        if ($request->expectsJson()) {
            return $this->jsonSuccess($checklists);
        }

        return view('v2.checklist.index', compact('checklists'));
    }

    public function create()
    {
        $this->authorize('create', Checklist::class);

        $categories = $this->checklistService->getCategories();

        return view('v2.checklist.form', ['checklist' => null, 'categories' => $categories]);
    }

    public function store(StoreChecklistRequest $request)
    {
        $this->authorize('create', Checklist::class);

        $checklist = $this->checklistService->create($request->validated());

        if ($request->expectsJson()) {
            return $this->jsonSuccess($checklist, 'Checklist berhasil dibuat.', 201);
        }

        return redirect()->route('v2.checklist.index')->with('success', 'Checklist berhasil dibuat.');
    }

    public function edit(Checklist $checklist)
    {
        $this->authorize('update', $checklist);

        return view('v2.checklist.form', [
            'checklist' => $this->checklistService->find($checklist->id),
            'categories' => $this->checklistService->getCategories(),
        ]);
    }

    public function update(UpdateChecklistRequest $request, Checklist $checklist)
    {
        $this->authorize('update', $checklist);

        $updated = $this->checklistService->update($checklist, $request->validated());

        if ($request->expectsJson()) {
            return $this->jsonSuccess($updated, 'Checklist berhasil diperbarui.');
        }

        return redirect()->route('v2.checklist.index')->with('success', 'Checklist berhasil diperbarui.');
    }

    public function destroy(Checklist $checklist)
    {
        $this->authorize('delete', $checklist);

        $this->checklistService->deactivate($checklist);

        return request()->expectsJson()
            ? $this->jsonSuccess(null, 'Checklist dinonaktifkan.')
            : back()->with('success', 'Checklist dinonaktifkan.');
    }
}
