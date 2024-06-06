<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Status;
use Illuminate\Console\Command;
use App\Events\NotifyCreaterEvent;

class CheckOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue tasks and notify product owners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overdueTasks = Task::where('due_date', '<', Carbon::now())
            ->where('status_id', '!=', Status::STATUS_DONE)
            ->get();

        foreach ($overdueTasks as $task) 
        {
            broadcast(new NotifyCreaterEvent($task->created_by));
        }
    }
}
