<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Todo'],
            ['name' => 'In progress'],
            ['name' => 'Ready for test'],
            ['name' => 'Po review'],
            ['name' => 'Done'],
            ['name' => 'Rejected'],
        ];

        foreach ($statuses as $status) {
            $name = $status['name'];
            
            Status::updateOrCreate(
                ['name' => $name], 
                $status
            );
        }
    }
}
