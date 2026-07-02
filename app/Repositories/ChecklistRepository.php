<?php

namespace App\Repositories;

use App\Models\Checklist;
use App\Models\ChecklistCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ChecklistRepository
{
    public function findCategoryById(int $id): ?ChecklistCategory
    {
        return ChecklistCategory::find($id);
    }

    public function findById(int $id): ?Checklist
    {
        return Checklist::with(['category', 'options'])->find($id);
    }

    public function findByCode(string $code): ?Checklist
    {
        return Checklist::where('code', $code)->first();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Checklist::query()
            ->with('category')
            ->when(isset($filters['category_id']), fn ($q) => $q->where('category_id', $filters['category_id']))
            ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', $filters['is_active']))
            ->when(! empty($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order');

        return $query->paginate($perPage);
    }

    public function getActiveWithCategories(): Collection
    {
        return ChecklistCategory::query()
            ->where('is_active', true)
            ->with(['checklists' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->with('options')])
            ->orderBy('sort_order')
            ->get();
    }

    public function getCategories(): Collection
    {
        return ChecklistCategory::query()->orderBy('sort_order')->get();
    }

    public function createCategory(array $data): ChecklistCategory
    {
        return ChecklistCategory::create($data);
    }

    public function updateCategory(ChecklistCategory $category, array $data): ChecklistCategory
    {
        $category->update($data);

        return $category->fresh();
    }

    public function create(array $data): Checklist
    {
        return Checklist::create($data);
    }

    public function update(Checklist $checklist, array $data): Checklist
    {
        $checklist->update($data);

        return $checklist->fresh(['category', 'options']);
    }

    public function deactivate(Checklist $checklist): Checklist
    {
        $checklist->update(['is_active' => false]);

        return $checklist->fresh();
    }
}
