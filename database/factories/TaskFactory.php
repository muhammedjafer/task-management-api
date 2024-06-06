<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Models\Status;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->text(),
            'status_id' => Status::STATUS_TODO,
            'due_date' => fake()->date(),
            'assigned_to' => User::firstWhere('role', RoleEnum::DEVELOPER->value)->id,
            'parent_id' => null,
            'created_by' => User::query()->first()->id
        ];
    }

    public function subtask(Task $parentTask)
    {
        return $this->state(function (array $attributes) use ($parentTask) {
            return [
                'parent_id' => $parentTask->id,
            ];
        });
    }
}
