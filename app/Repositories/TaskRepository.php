<?php

namespace App\Repositories;

use App\Filters\TaskFilter;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<Task>
     */
    public function index(array $filters = []): LengthAwarePaginator
    {
        $query = Task::with('user');

        $taskFilter = new TaskFilter($filters);
        $filteredQuery = $taskFilter->apply($query);

        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $filteredQuery->paginate($perPage, ['*'], 'page', $page);
    }

    public function show(int $id): Task
    {
        try {
            return Task::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Task not found: ' . $e->getMessage());
        }
    }

    public function store(array $data): Task
    {
        $collectedData = collect($data);
        try {
            return Task::create($collectedData->toArray());
        } catch (Exception $e) {
            throw new Exception('Failed to create task: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): Task
    {
        try {
            $task = Task::findOrFail($id);
            $task->update($data);
            return $task;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update task: ' . $e->getMessage());
        }
    }

    public function addUsedTimeToTask(int $id, int $time): Task
    {
        try {
            return tap(Task::findOrFail($id), function ($task) use ($time) {
                $task->increment('used_time', $time);
            });
        } catch (ModelNotFoundException $e) {
            throw new Exception('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update task: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): bool|null
    {
        try {
            $task = Task::findOrFail($id);
            return $task->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete task: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(array $ids): bool
    {
        try {
            Task::whereIn('id', $ids)->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to delete task: ' . $e->getMessage());
        }
    }

    public function toggleCompleted(int $id): Task
    {
        try {
            $task = Task::findOrFail($id);
            if ($task->is_completed) {
                return $task->setUnCompleted();
            } else {
                return $task->setCompleted();
            }
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to set completed task: ' . $e->getMessage());
        }
    }

    public function setBulkCompleted(array $ids): string
    {
        $now = Carbon::now();
        try {
            Task::whereIn('id', $ids)
                ->whereNull('completed_at')
                ->update(['completed_at' => $now]);
            return $now;
        } catch (Exception $e) {
            throw new Exception('Failed to set completed tasks: ' . $e->getMessage());
        }
    }
}
