<?php

namespace App\Repositories;

use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Carbon\Carbon;

class TaskRepository implements TaskRepositoryInterface
{
    /** @return EloquentCollection<int, Task> */
    public function index(): EloquentCollection
    {
        return Task::all();
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

        $collectedData = collect($data);

        try {
            $task = Task::findOrFail($id);

            if ($collectedData->has('used_time')) {
                $task->used_time += $collectedData->get('used_time');

                $collectedData->forget('used_time');
            }

            $task->fill($collectedData->toArray());

            $task->save();
            return $task;
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

    public function setCompleted(int $id): Task
    {
        try {
            $task = Task::findOrFail($id);
            return $task->setCompleted();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to set completed task: ' . $e->getMessage());
        }
    }

    public function setBulkCompleted(array $ids): bool
    {
        try {
            Task::whereIn('id', $ids)->update(['completed_at' => Carbon::now()]);
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to set completed tasks: ' . $e->getMessage());
        }
    }
}
