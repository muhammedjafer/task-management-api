<?php

namespace App\Console\Commands;

use App\Jobs\ImportTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class ImportTasksToCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:tasks {file : The CSV file to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tasks from a CSV file into the database asynchronously using batch jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!Storage::exists($filePath)) {
            $this->error("The file {$filePath} does not exist.");
            return;
        }

        $file = fopen(Storage::path($filePath), 'r');
        $header = fgetcsv($file);

        $jobs = [];
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);
            $jobs[] = new ImportTask($data);
        }
        fclose($file);

        // Create a batch and track progress
        $batch = Bus::batch($jobs)
            ->name('ImportTasksBatch')
            ->onQueue('imports')
            ->dispatch();

        $this->info("Import tasks from {$filePath} dispatched successfully. Batch ID: {$batch->id}");
    }
}
