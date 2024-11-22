<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Task;

interface TaskRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<Task>
     */
    public function index(array $filters = []): LengthAwarePaginator;
    public function show(int $id): Task;
    public function store(array $data): Task;
    public function update(int $id, array $data): Task;
    public function destroy(int $id): bool|null;
    public function bulkDestroy(array $ids): bool;
    public function toggleCompleted(int $id): Task;
    public function setBulkCompleted(array $ids): string;
    public function addUsedTimeToTask(int $id, int $time): Task;
}
