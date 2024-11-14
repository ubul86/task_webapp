<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Models\Task;

interface TaskRepositoryInterface
{
    /** @return EloquentCollection<int, Task> */
    public function index(): EloquentCollection;
    public function show(int $id): Task;
    public function store(array $data): Task;
    public function update(int $id, array $data): Task;
    public function destroy(int $id): bool|null;
    public function bulkDestroy(array $ids): bool;
    public function setCompleted(int $id): Task;
    public function setBulkCompleted(array $ids): bool;
}
