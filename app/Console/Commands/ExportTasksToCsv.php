<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class ExportTasksToCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:tasks {file=exported_tasks.csv : The name of the exported CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export tasks to a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tasks = Task::all();

        $csvData = [];

        //The header of the file
        $csvData[] = ['ID', 'Title', 'Description', 'Status', 'Due date', 'Created by'];

        foreach ($tasks as $task) {
            $csvData[] = [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status_id,
                'due_date' => $task->due_date,
                'created_by' => $task->created_by
            ];
        }

        $fileName = $this->argument('file');

        $csvFile = fopen(storage_path("app/{$fileName}"), 'w');
        foreach ($csvData as $rowData) {
            fputcsv($csvFile, $rowData);
        }
        fclose($csvFile);

        $this->info("Tasks exported to {$fileName}");
    }
}
