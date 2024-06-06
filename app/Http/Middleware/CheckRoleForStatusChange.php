<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Status;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleForStatusChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $status_id = $request->status_id;
        $user_role = auth('sanctum')->user()->role;
        $task = $request->route('task');
        
        if (is_null($status_id) || $status_id == '' || !in_array($status_id, array_keys(Status::STATUS)))
        {
            return response()->streamJson([
                'message' => 'Status not found'
            ], 403);
        }

        if ($user_role == RoleEnum::DEVELOPER->value)
        {
            $check_status = isset(Status::DEVELOPER_STATUSES[$task->status_id]);
            $change_task_status_to = isset(Status::DEVELOPER_CHANGE_STATUS_TO[$status_id]);
        }
        else if ($user_role == RoleEnum::TESTER->value)
        {
            $check_status = ($task->status_id == Status::STATUS_TODO && $status_id == Status::STATUS_IN_PROGRESS)
                ? true
                : isset(Status::TESTER_STATUSES[$task->status_id]);

            $change_task_status_to = ($status_id == Status::STATUS_PO_REVIEW) ? true : false;
        }
        else {
            $check_status = ($task->status_id == Status::STATUS_TODO && $status_id == Status::STATUS_IN_PROGRESS)
                ? false
                : true;
                
            $change_task_status_to = isset(Status::PRODUCT_OWNER_CHANGE_STATUS_TO[$status_id]);
        }

        if (!$check_status || !$change_task_status_to)
        {
            return response()->streamJson([
                'message' => 'Access denied'
            ], 403);
        }
        return $next($request);
    }
}
