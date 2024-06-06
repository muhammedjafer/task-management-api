<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::factory()
            ->count(10)
            ->create()
            ->each(function ($task) {
                
                Task::factory()
                    ->count(rand(1, 3))
                    ->subtask($task)
                    ->create();
            });
    }
}
