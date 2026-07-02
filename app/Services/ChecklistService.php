<?php

namespace App\Services;

use App\Models\Checklist;
use App\Models\ChecklistCategory;
use App\Repositories\ChecklistRepository;
use App\Support\ChecklistCodeGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ChecklistService
{
    public function __construct(
        private readonly ChecklistRepository $checklistRepository,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->checklistRepository->paginate($filters, $perPage);
    }

    public function getActiveForInspection(): Collection
    {
        return $this->checklistRepository->getActiveWithCategories();
    }

    public function getCategories(): Collection
    {
        return $this->checklistRepository->getCategories();
    }

    public function find(int $id): ?Checklist
    {
        return $this->checklistRepository->findById($id);
    }

    public function createCategory(array $data): ChecklistCategory
    {
        return DB::transaction(function () use ($data) {
            $category = $this->checklistRepository->createCategory($data);
            $this->auditLogService->log('checklist', 'create', "menambahkan kategori daftar periksa \"{$category->name}\"");

            return $category;
        });
    }

    public function updateCategory(ChecklistCategory $category, array $data): ChecklistCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $updated = $this->checklistRepository->updateCategory($category, $data);
            $this->auditLogService->log('checklist', 'update', "memperbarui kategori daftar periksa \"{$updated->name}\"");

            return $updated;
        });
    }

    public function create(array $data): Checklist
    {
        $category = $this->checklistRepository->findCategoryById($data['category_id']);

        if ($category === null) {
            throw new InvalidArgumentException('Kategori tidak ditemukan.');
        }

        $data['code'] = ChecklistCodeGenerator::generate($category);

        return DB::transaction(function () use ($data) {
            $checklist = $this->checklistRepository->create($data);
            $this->auditLogService->log('checklist', 'create', "menambahkan item daftar periksa \"{$checklist->title}\" ({$checklist->code})");

            return $checklist;
        });
    }

    public function update(Checklist $checklist, array $data): Checklist
    {
        unset($data['code']);

        return DB::transaction(function () use ($checklist, $data) {
            $updated = $this->checklistRepository->update($checklist, $data);
            $this->auditLogService->log('checklist', 'update', "memperbarui item daftar periksa \"{$updated->title}\" ({$updated->code})");

            return $updated;
        });
    }

    public function deactivate(Checklist $checklist): Checklist
    {
        return DB::transaction(function () use ($checklist) {
            $deactivated = $this->checklistRepository->deactivate($checklist);
            $this->auditLogService->log('checklist', 'update', "menonaktifkan item daftar periksa \"{$deactivated->title}\"");

            return $deactivated;
        });
    }
}
