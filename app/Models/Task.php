<?php

namespace App\Models;

use App\Models\User;
use App\Models\Status;
use App\Models\LogTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'status_id'
    ];

    public function logs()
    {
        return $this->hasMany(LogTask::class);
    }

    public function subtasks()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parentTask()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
