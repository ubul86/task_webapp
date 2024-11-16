<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUsedTimeRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function index(): JsonResponse
    {
        $tasks = $this->taskRepository->index();
        return response()->json(TaskResource::collection($tasks));
    }

    public function show(int $id): JsonResponse
    {
        try {
            $task = $this->taskRepository->show($id);
            return response()->json(new TaskResource($task));
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $task = $this->taskRepository->store($validated);
            return response()->json(new TaskResource($task), 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $task = $this->taskRepository->update($id, $validated);
            return response()->json(new TaskResource($task));
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function addUsedTimeToTask(AddUsedTimeRequest $request, int $id): JsonResponse
    {
        try {
            $validated = collect($request->validated());
            $task = $this->taskRepository->addUsedTimeToTask($id, $validated->get('used_time'));
            return response()->json(new TaskResource($task));
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->taskRepository->destroy($id);
            return response()->json(['message' => 'Task deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        try {
            $ids = explode(',', $request->input('ids'));
            $this->taskRepository->bulkDestroy($ids);
            return response()->json(['message' => 'Tasks deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function setCompleted(int $id): JsonResponse
    {
        try {
            $task = $this->taskRepository->setCompleted($id);
            return response()->json(new TaskResource($task));
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function setBulkCompleted(Request $request): JsonResponse
    {
        try {
            $ids = explode(',', $request->input('ids'));
            $this->taskRepository->setBulkCompleted($ids);
            return response()->json(['message' => 'Selected tasks set to completed successfully']);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }
}
