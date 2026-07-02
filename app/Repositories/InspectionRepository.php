<?php

namespace App\Repositories;

use App\Models\Inspection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InspectionRepository
{
    public function findById(int $id): ?Inspection
    {
        return Inspection::with([
            'travel',
            'creator',
            'updater',
            'checklists.masterChecklist.category',
            'checklists.masterChecklist.options',
            'findings.followups',
            'photos',
        ])->find($id);
    }

    public function findByInspectionNo(string $inspectionNo): ?Inspection
    {
        return Inspection::where('inspection_no', $inspectionNo)->first();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Inspection::query()
            ->with(['travel', 'creator'])
            ->when(isset($filters['travel_id']), fn ($q) => $q->where('travel_id', $filters['travel_id']))
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['inspection_type']), fn ($q) => $q->where('inspection_type', $filters['inspection_type']))
            ->when(isset($filters['kabupaten']), function ($q) use ($filters) {
                $q->whereHas('travel', fn ($travel) => $travel->where('kab_kota', $filters['kabupaten']));
            })
            ->when(isset($filters['date_from']), fn ($q) => $q->whereDate('inspection_date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn ($q) => $q->whereDate('inspection_date', '<=', $filters['date_to']))
            ->when(! empty($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where(function ($inner) use ($search) {
                    $inner->where('inspection_no', 'like', "%{$search}%")
                        ->orWhereHas('travel', fn ($travel) => $travel->where('Penyelenggara', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('inspection_date');

        return $query->paginate($perPage);
    }

    public function listForExport(array $filters = []): Collection
    {
        return Inspection::query()
            ->with(['travel', 'creator'])
            ->withCount('findings')
            ->when(isset($filters['travel_id']), fn ($q) => $q->where('travel_id', $filters['travel_id']))
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['inspection_type']), fn ($q) => $q->where('inspection_type', $filters['inspection_type']))
            ->when(isset($filters['kabupaten']), function ($q) use ($filters) {
                $q->whereHas('travel', fn ($travel) => $travel->where('kab_kota', $filters['kabupaten']));
            })
            ->when(isset($filters['date_from']), fn ($q) => $q->whereDate('inspection_date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn ($q) => $q->whereDate('inspection_date', '<=', $filters['date_to']))
            ->when(! empty($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where(function ($inner) use ($search) {
                    $inner->where('inspection_no', 'like', "%{$search}%")
                        ->orWhereHas('travel', fn ($travel) => $travel->where('Penyelenggara', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('inspection_date')
            ->get();
    }

    public function getByTravel(int $travelId): Collection
    {
        return Inspection::query()
            ->where('travel_id', $travelId)
            ->with(['findings'])
            ->orderByDesc('inspection_date')
            ->get();
    }

    public function create(array $data): Inspection
    {
        return Inspection::create($data);
    }

    public function update(Inspection $inspection, array $data): Inspection
    {
        $inspection->update($data);

        return $inspection->fresh(['travel', 'creator', 'updater']);
    }

    public function countByStatus(?string $status = null): int
    {
        return Inspection::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->count();
    }

    public function createFinding(array $data): \App\Models\InspectionFinding
    {
        return \App\Models\InspectionFinding::create($data);
    }

    public function updateFinding(\App\Models\InspectionFinding $finding, array $data): \App\Models\InspectionFinding
    {
        $finding->update($data);

        return $finding->fresh();
    }

    public function updateChecklist(\App\Models\InspectionChecklist $checklist, array $data): \App\Models\InspectionChecklist
    {
        $checklist->update($data);

        return $checklist->fresh(['masterChecklist.options']);
    }
}
