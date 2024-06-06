<?php

namespace App\Http\Requests\Task;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $task = $this->route('task');
        $check_required_or_not = ($task->status_id == Status::STATUS_TODO) ? 'nullable' : 'required';

        return [
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'assigned_to' => "{$check_required_or_not}|exists:users,id",
            'due_date' => 'nullable|date',
            'sub_tasks' => 'nullable|array',
            'sub_tasks.*' => 'sometimes|required|exists:tasks,id'
        ];
    }
}
