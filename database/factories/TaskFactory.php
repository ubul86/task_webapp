<?php
namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        $completedAt = $this->faker->optional()->dateTime;

        return [
            'user_id' => $this->faker->randomElement([null, User::inRandomOrder()->first()?->id]),
            'description' => $this->faker->sentence,
            'estimated_time' => $this->faker->numberBetween(1000, 10000),
            'used_time' => $this->faker->numberBetween(10, 10000),
            'completed_at' => $completedAt,
            'is_completed' => $completedAt ? true : false
        ];
    }
}
