<?php

namespace App\Http\Controllers\Task;

use App\Models\Task;
use App\Models\User;
use App\Models\Status;
use App\Enums\RoleEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;

class TaskController extends Controller
{
    public function taskDetails(Task $task)
    {
        $task = Task::select('id', 'title', 'description', 'assigned_to', 'created_at', 'due_date', 'status_id', 'parent_id')
        ->with([
            'status:id,name',
            'logs:id,task_id,action',
            'subtasks:id,title,description,status_id,assigned_to,parent_id',
            'subtasks.status:id,name', 
            'subtasks.assignedTo:id,name,email', 
            'assignedTo:id,name,email' 
        ])
        ->findOrFail($task->id);

        return response()->streamJson([
            'data' => $task
        ], 200);
    }

    public function taskList(Request $request)
    {
        $query = Task::select('id', 'title', 'description', 'status_id', 'assigned_to')
            ->with(['status:id,name', 'assignedTo:id,name,email']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereAny(
                    [
                        'title',
                        'description',
                        'id'
                    ], 
                    'LIKE',
                    "%$search%"
                );
            });
        }

        if ($request->has('assigned_to')) {
            $assignedTo = $request->input('assigned_to');
            $query->whereIn('assigned_to', $assignedTo);
        }

        if ($request->has('assignee_name')) {
            $assigneeName = $request->input('assignee_name');
            $query->whereHas('assignedTo', function ($q) use ($assigneeName) {
                $q->where('name', 'like', "%$assigneeName%");
            });
        }

        return response()->streamJson([
            'data' => $query->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTaskRequest $request)
    {
        try {

            DB::beginTransaction();
            
            $task = Task::create([
                'title' => Str::trim($request->title),
                'description' => Str::trim($request->description),
                'status_id' => Status::STATUS_TODO,
                'due_date' => $request->due_date,
                'assigned_to' => $request->assigned_to,
                'created_by' => auth('sanctum')->user()->id
            ]);
    
            if ($request->sub_tasks)
            {
                Task::whereIn('id', $request->sub_tasks)->update([
                    'parent_id' => $task->id
                ]);
            }

            DB::commit();

        } catch (\Throwable $th) {
            
            DB::rollback();

            return response()->streamJson([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->streamJson([
            'message' => 'Task created successfully'
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {

            DB::beginTransaction();
            
            $old_title = $task->title;
            $old_due_date = $task->due_date;
            $old_description = $task->description;

            $task->update([
                'assigned_to' => $request->assigned_to ?? $task->assigned_to,
                'due_date' => $request->due_date ?? $task->due_date,
                'title' => Str::trim($request->title) ?: $task->title,
                'description' => Str::trim($request->description) ?: $task->description
            ]);

            if ($task->wasChanged('title'))
            {
                $task->logs()->create([
                    'changed_by' => auth('sanctum')->user()->id,
                    'action' => 'changing the title',
                    'old_value' => $old_title,
                    'new_value' => $task->title
                ]);
            }

            if ($task->wasChanged('description'))
            {
                $task->logs()->create([
                    'changed_by' => auth('sanctum')->user()->id,
                    'action' => 'changing the description',
                    'old_value' => $old_description,
                    'new_value' => $task->description
                ]);
            }

            if ($task->wasChanged('due_date'))
            {
                $task->logs()->create([
                    'changed_by' => auth('sanctum')->user()->id,
                    'action' => 'changing the due date',
                    'old_value' => $old_due_date,
                    'new_value' => $task->due_date
                ]);
            }
    
            if ($request->sub_tasks)
            {
                //Returning sub tasks without the current task ID since a parent task cannot also be its own subtask.
                $sub_tasks = Arr::where($request->sub_tasks, function ($value, $key) use ($task) {
                    return $value != $task->id;
                });
    
                Task::whereIn('id', $sub_tasks)->update([
                    'parent_id' => $task->id
                ]);
            }

            DB::commit();

        } catch (\Throwable $th) {
            
            DB::rollback();
            
            return response()->streamJson([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->streamJson([
            'message' => 'Task updated successfully'
        ], 200);

    }

    public function changeStatus(Request $request, Task $task)
    {
        $statusId = $request->status_id;
        $old_status = Status::STATUS[$task->status_id];

        if (($task->status_id != Status::STATUS_TODO) && is_null($task->assigned_to))
        {
            return response()->streamJson([
                'message' => 'The task is not assigned to user, can not update status'
            ], 400);
        }

        //Only user with developer role can perform this action
        if ($task->status_id == Status::STATUS_TODO && $statusId == Status::STATUS_IN_PROGRESS)
        {
            $task->update([
                'status_id' => $statusId
            ]);
        }
        //Only user with developer role can perform this action
        else if ($task->status_id == Status::STATUS_IN_PROGRESS && $statusId == Status::STATUS_READY_FOR_TEST)
        {
            $testerWithLeastTasks = User::where('role', RoleEnum::TESTER->value)
                ->withCount('tasks')
                ->orderBy('tasks_count', 'asc')
                ->first();

            $task->update([
                'status_id' => $statusId,
                'assigned_to' => $testerWithLeastTasks->id,
                'done_by' => auth('sanctum')->user()->id
            ]);
        }
        //Only user with tester role can perform this action
        else if ($task->status_id == Status::STATUS_READY_FOR_TEST && $statusId == Status::STATUS_PO_REVIEW)
        {
            $task->update([
                'status_id' => $statusId,
                'assigned_to' => $task->created_by
            ]);
        }
        //Only user with product owner role can perform this action
        else if ($task->status_id == Status::STATUS_PO_REVIEW && $statusId == Status::STATUS_DONE)
        {
            $task->update([
                'status_id' => $statusId,
                'assigned_to' => $task->done_by
            ]);
        }
        //Only user with prodcut owner role can perform this action
        else if ($task->status_id != Status::STATUS_TODO && $statusId == Status::STATUS_IN_PROGRESS)
        {
            $task->update([
                'status_id' => $statusId,
                'assigned_to' => $task->done_by
            ]);
        }
        //Only user with product owner role can perform this action
        else if ($statusId == Status::STATUS_REJECTED)
        {
            $task->update([
                'status_id' => $statusId
            ]);
        }
        else {
            return response()->streamJson([
                'message' => 'Something went wrong'
            ], 400);
        }

        $task->logs()->create([
            'changed_by' => auth('sanctum')->user()->id,
            'action' => 'changing the status to '.Status::STATUS[$statusId],
            'old_value' => $old_status,
            'new_value' => Status::STATUS[$task->status_id]
        ]);

        return response()->streamJson([
            'message' => 'Task status updated successfully'
        ], 200);
    }

    public function getProgress($id)
    {
        $batch = Bus::findBatch($id);
        
        if (!$batch) {
            return response()->streamJson([
                'message' => 'Batch not found'
            ], 404);
        }

        return response()->streamJson([
            'id' => $batch->id,
            'name' => $batch->name,
            'totalJobs' => $batch->totalJobs,
            'pendingJobs' => $batch->pendingJobs,
            'failedJobs' => $batch->failedJobs,
            'processedJobs' => $batch->processedJobs(),
            'progress' => $batch->progress(),
            'status' => $batch->finished() ? 'Finished' : ($batch->cancelled() ? 'Cancelled' : 'In Progress'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Task::where('parent_id', $task->id)->update([
            'parent_id' => null
        ]);
        
        $task->delete();

        return response()->streamJson([
            'message' => 'Task deleted successfully'
        ], 200);
    }
}
