<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskRepositoryTest extends TestCase
{
    protected TaskRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TaskRepository();
    }

    public function testReturnASpecificTask(): void
    {
        $task = Task::factory()->create();

        $retrievedTask = $this->repository->show($task->id);

        $this->assertInstanceOf(Task::class, $retrievedTask);
        $this->assertEquals($task->id, $retrievedTask->id);
    }

    public function testThrowsExceptionWhenTaskNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->show(-1);
    }

    public function testCreateTask(): void
    {
        $data = [
            'description' => 'Test Description',
            'estimated_time' => 50,
            'used_time' => 0,
        ];

        $task = $this->repository->store($data);

        $this->assertDatabaseHas('tasks', ['description' => 'Test Description']);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Description', $task->description);
    }

    public function testUpdateTask(): void
    {
        $task = Task::factory()->create();

        $updatedData = ['description' => 'Updated Task Description'];

        $updatedTask = $this->repository->update($task->id, $updatedData);

        $this->assertDatabaseHas('tasks', ['description' => 'Updated Task Description']);
        $this->assertEquals('Updated Task Description', $updatedTask->description);
    }

    public function testDeleteTask(): void
    {
        $task = Task::factory()->create();

        $result = $this->repository->destroy($task->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function testBulkDeleteTasks(): void
    {
        $tasks = Task::factory()->count(5)->create();

        $ids = $tasks->pluck('id')->toArray();

        $result = $this->repository->bulkDestroy($ids);

        $this->assertTrue($result);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('tasks', ['id' => $id]);
        }
    }
}
