<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V2\Concerns\RespondsWithJson;
use App\Models\AuditLog;
use App\Repositories\AuditLogRepository;
use App\Support\AuditLogNarrator;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    use RespondsWithJson;

    public function __construct(
        private readonly AuditLogRepository $auditLogRepository,
        private readonly AuditLogNarrator $auditLogNarrator,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $filters = $this->resolveFilters($request);
        $logs = $this->auditLogRepository->paginate($filters);

        $presented = $logs->through(fn (AuditLog $log) => [
            'id' => $log->id,
            'created_at' => $log->created_at?->toIso8601String(),
            'created_at_label' => $log->created_at?->format('d/m/Y H:i'),
            ...$this->auditLogNarrator->present($log),
        ]);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($presented);
        }

        return view('v2.audit-log.index', [
            'logs' => $presented,
            'filters' => $filters,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function show(Request $request, AuditLog $auditLog)
    {
        $this->authorize('view', $auditLog);

        $log = $this->auditLogRepository->findById($auditLog->id);
        $narrative = $this->auditLogNarrator->present($log);

        if ($request->expectsJson()) {
            return $this->jsonSuccess([
                'log' => $log,
                'narrative' => $narrative,
            ]);
        }

        return view('v2.audit-log.show', compact('log', 'narrative'));
    }

    /** @return array<string, mixed> */
    private function resolveFilters(Request $request): array
    {
        $filters = $request->only(['module', 'action', 'user_id', 'date_from', 'date_to', 'q']);
        $user = $request->user();

        if ($user->role === 'kabupaten') {
            $filters['kabupaten'] = $user->getKabupaten();
        }

        return $filters;
    }

    /** @return array<string, string> */
    private function categoryOptions(): array
    {
        return [
            '' => 'Semua kegiatan',
            'pengawasan' => 'Pengawasan',
            'followup' => 'Tindak Lanjut',
            'checklist' => 'Daftar Periksa',
            'risk' => 'Penilaian Risiko',
            'auth' => 'Masuk & Keluar Sistem',
        ];
    }
}
