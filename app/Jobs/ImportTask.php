<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $data
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Task::create([
            'title' => $this->data['Title'],
            'description' => $this->data['Description'],
            'status_id' => $this->data['Status'],
            'due_date' => $this->data['Due date'],
            'created_by' => $this->data['Created by'],
        ]);
    }
}
