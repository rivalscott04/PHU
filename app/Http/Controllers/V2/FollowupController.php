<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Http\Requests\StoreFollowupRequest;
use App\Models\Followup;
use App\Models\InspectionFinding;
use App\Repositories\FollowupRepository;
use App\Services\AuditLogService;
use App\Services\FollowupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class FollowupController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly FollowupService $followupService,
        private readonly FollowupRepository $followupRepository,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Followup::class);

        $filters = $this->scopeFilters($request, ['status', 'finding_id']);
        $followups = $this->followupService->paginate($filters);
        $statusCounts = $this->followupRepository->countByStatus($filters);
        $cards = $this->buildStatusCards($statusCounts);

        if ($request->expectsJson()) {
            return $this->jsonSuccess([
                'followups' => $followups,
                'summary' => $cards,
            ]);
        }

        return view('v2.followup.index', compact('followups', 'cards', 'filters'));
    }

    public function show(Request $request, Followup $followup)
    {
        $this->authorize('view', $followup);

        $followup = $this->followupService->find($followup->id);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($followup);
        }

        return view('v2.followup.show', compact('followup'));
    }

    public function store(StoreFollowupRequest $request)
    {
        $this->authorize('create', Followup::class);

        $finding = InspectionFinding::with('inspection')->findOrFail($request->validated('finding_id'));

        abort_unless(
            $finding->inspection?->travel_id === $request->user()->travel_id,
            403,
            'Temuan ini bukan milik travel Anda.'
        );

        $attachmentPath = $request->file('attachment')->store('followups');

        try {
            $followup = $this->followupService->submit($finding, [
                'description' => $request->validated('description'),
                'attachment' => $attachmentPath,
            ]);
        } catch (ConflictHttpException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 409)
                : back()->withErrors(['attachment' => $e->getMessage()])->withInput();
        }

        if ($request->expectsJson()) {
            return $this->jsonSuccess($followup, 'Tindak lanjut berhasil diunggah.', 201);
        }

        return redirect()->route('v2.followup.show', $followup)
            ->with('success', 'Tindak lanjut berhasil diunggah.');
    }

    public function download(Request $request, Followup $followup)
    {
        $this->authorize('view', $followup);

        abort_unless($followup->attachment, 404);
        abort_unless(Storage::exists($followup->attachment), 404);

        $followup->load('finding');
        $this->auditLogService->log(
            'followup',
            'download',
            "mengunduh lampiran tindak lanjut untuk temuan \"{$followup->finding?->title}\""
        );

        return Storage::download($followup->attachment);
    }

    public function approve(Request $request, Followup $followup)
    {
        $this->authorize('approve', $followup);

        $data = $request->validate(['remarks' => ['nullable', 'string']]);

        try {
            $updated = $this->followupService->approve($followup, $data['remarks'] ?? null);
        } catch (ConflictHttpException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 409)
                : back()->withErrors(['remarks' => $e->getMessage()]);
        }

        return $request->expectsJson()
            ? $this->jsonSuccess($updated, 'Tindak lanjut disetujui.')
            : back()->with('success', 'Tindak lanjut disetujui.');
    }

    public function revision(Request $request, Followup $followup)
    {
        $this->authorize('approve', $followup);

        $data = $request->validate(['remarks' => ['required', 'string', 'min:10']]);

        try {
            $updated = $this->followupService->requestRevision($followup, $data['remarks']);
        } catch (ConflictHttpException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 409)
                : back()->withErrors(['remarks' => $e->getMessage()]);
        }

        return $request->expectsJson()
            ? $this->jsonSuccess($updated, 'Revisi diminta.')
            : back()->with('success', 'Revisi diminta.');
    }

    /** @return array<string, array{label: string, value: int, icon: string, color: string}> */
    private function buildStatusCards(\Illuminate\Support\Collection $statusCounts): array
    {
        $waiting = (int) ($statusCounts['SUBMITTED'] ?? 0) + (int) ($statusCounts['PENDING'] ?? 0);

        return [
            'waiting' => [
                'label' => 'Menunggu Verifikasi',
                'value' => $waiting,
                'icon' => 'bx-time-five',
                'color' => '#f1b44c',
            ],
            'revision' => [
                'label' => 'Perlu Revisi',
                'value' => (int) ($statusCounts['REVISION_REQUIRED'] ?? 0),
                'icon' => 'bx-revision',
                'color' => '#f46a6a',
            ],
            'verified' => [
                'label' => 'Terverifikasi',
                'value' => (int) ($statusCounts['VERIFIED'] ?? 0),
                'icon' => 'bx-check-circle',
                'color' => '#34c38f',
            ],
            'closed' => [
                'label' => 'Selesai',
                'value' => (int) ($statusCounts['CLOSED'] ?? 0),
                'icon' => 'bx-archive',
                'color' => '#74788d',
            ],
        ];
    }
}
