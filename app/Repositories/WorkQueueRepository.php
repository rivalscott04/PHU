<?php

namespace App\Repositories;

use App\Enums\WorkQueueStatus;
use App\Models\SupervisionWorkQueue;
use App\Support\KabupatenScopeFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class WorkQueueRepository
{
    public function upsert(array $attributes, array $values): SupervisionWorkQueue
    {
        return SupervisionWorkQueue::query()->updateOrCreate($attributes, $values);
    }

    public function findByReference(string $type, string $referenceType, int $referenceId): ?SupervisionWorkQueue
    {
        return SupervisionWorkQueue::query()
            ->where('type', $type)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->first();
    }

    /** @return array<string, int> */
    public function countOpenByType(array $filters = []): array
    {
        $query = $this->openQuery($filters);

        return $query
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SupervisionWorkQueue::query()
            ->with('travel');

        KabupatenScopeFilter::applyOnColumn($query, $filters, 'kabupaten');

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->whereIn('status', [WorkQueueStatus::Open->value, WorkQueueStatus::InProgress->value]);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', '>=', (int) $filters['priority']);
        }

        return $query
            ->orderByDesc('priority')
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @param array<string, mixed> $filters */
    private function openQuery(array $filters): Builder
    {
        $query = SupervisionWorkQueue::query()
            ->whereIn('status', [WorkQueueStatus::Open->value, WorkQueueStatus::InProgress->value]);

        KabupatenScopeFilter::applyOnColumn($query, $filters, 'kabupaten');

        return $query;
    }
}
