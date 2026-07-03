<?php

namespace App\Repositories;

use App\Models\Followup;
use App\Models\FollowupLog;
use App\Support\KabupatenScopeFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class FollowupRepository
{
    public function findById(int $id): ?Followup
    {
        return Followup::with([
            'finding.inspection.travel',
            'verifier',
            'logs.creator',
        ])->find($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Followup::query()
            ->with(['finding.inspection.travel', 'verifier'])
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['finding_id']), fn ($q) => $q->where('finding_id', $filters['finding_id']))
            ->when(isset($filters['travel_id']), function ($q) use ($filters) {
                $q->whereHas('finding.inspection', fn ($inspection) => $inspection->where('travel_id', $filters['travel_id']));
            })
            ->when(! empty($filters['kabupaten']) || ! empty($filters['kabupatens']), function ($q) use ($filters) {
                KabupatenScopeFilter::applyOnTravelRelation($q, $filters, 'finding.inspection.travel');
            })
            ->orderByDesc('submitted_at');

        return $query->paginate($perPage);
    }

    public function create(array $data): Followup
    {
        return Followup::create($data);
    }

    public function update(Followup $followup, array $data): Followup
    {
        $followup->update($data);

        return $followup->fresh(['finding', 'verifier']);
    }

    public function addLog(Followup $followup, array $data): FollowupLog
    {
        return $followup->logs()->create($data);
    }

    public function getTimeline(int $followupId): Collection
    {
        return FollowupLog::query()
            ->where('followup_id', $followupId)
            ->with('creator')
            ->orderBy('created_at')
            ->get();
    }

    public function countByStatus(array $filters = []): SupportCollection
    {
        return Followup::query()
            ->when(isset($filters['travel_id']), function ($q) use ($filters) {
                $q->whereHas('finding.inspection', fn ($inspection) => $inspection->where('travel_id', $filters['travel_id']));
            })
            ->when(! empty($filters['kabupaten']) || ! empty($filters['kabupatens']), function ($q) use ($filters) {
                KabupatenScopeFilter::applyOnTravelRelation($q, $filters, 'finding.inspection.travel');
            })
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }
}
