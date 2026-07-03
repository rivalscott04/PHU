<?php

namespace App\Repositories;

use App\Enums\RiskLevel;
use App\Models\RiskScore;
use App\Support\KabupatenScopeFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RiskRepository
{
    public function findByTravelId(int $travelId): ?RiskScore
    {
        return RiskScore::with('travel')->where('travel_id', $travelId)->first();
    }

    public function upsertForTravel(int $travelId, array $data): RiskScore
    {
        return RiskScore::updateOrCreate(
            ['travel_id' => $travelId],
            $data
        );
    }

    public function getRanking(int $limit = 10, ?string $kabupaten = null): Collection
    {
        return RiskScore::query()
            ->with('travel')
            ->when($kabupaten, fn ($q) => $q->whereHas('travel', fn ($travel) => $travel->where('kab_kota', $kabupaten)))
            ->orderByDesc('total_score')
            ->limit($limit)
            ->get();
    }

    public function getHighRiskTravels(?string $kabupaten = null): Collection
    {
        return RiskScore::query()
            ->with('travel')
            ->whereIn('risk_level', [RiskLevel::High->value, RiskLevel::Critical->value])
            ->when($kabupaten, fn ($q) => $q->whereHas('travel', fn ($travel) => $travel->where('kab_kota', $kabupaten)))
            ->orderByDesc('total_score')
            ->get();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return RiskScore::query()
            ->with('travel')
            ->when(isset($filters['risk_level']), fn ($q) => $q->where('risk_level', $filters['risk_level']))
            ->when(! empty($filters['kabupaten']) || ! empty($filters['kabupatens']), function ($q) use ($filters) {
                KabupatenScopeFilter::applyOnTravelRelation($q, $filters);
            })
            ->orderByDesc('total_score')
            ->paginate($perPage);
    }

    public function countByRiskLevel(array $filters = []): array
    {
        return RiskScore::query()
            ->when(! empty($filters['kabupaten']) || ! empty($filters['kabupatens']), function ($q) use ($filters) {
                KabupatenScopeFilter::applyOnTravelRelation($q, $filters);
            })
            ->when(isset($filters['travel_id']), fn ($q) => $q->where('travel_id', $filters['travel_id']))
            ->selectRaw('risk_level, COUNT(*) as total')
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level')
            ->toArray();
    }
}
