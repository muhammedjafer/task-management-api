<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    const STATUS_TODO = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_READY_FOR_TEST = 3;
    const STATUS_PO_REVIEW = 4;
    const STATUS_DONE = 5;
    const STATUS_REJECTED = 6;

    const STATUS = [
        self::STATUS_TODO => 'To do',
        self::STATUS_IN_PROGRESS => 'In progress',
        self::STATUS_READY_FOR_TEST => 'Ready for test',
        self::STATUS_PO_REVIEW => 'Po review',
        self::STATUS_DONE => 'Done',
        self::STATUS_REJECTED => 'Rejected',
    ];

    //Statuses a user with developer role is allowed for tasks.
    const DEVELOPER_STATUSES = [
        self::STATUS_IN_PROGRESS => 'In progress',
        self::STATUS_TODO => 'To do',
    ];

    //Statuses a user with developer role is allowed for tasks to change the status.
    const DEVELOPER_CHANGE_STATUS_TO = [
        self::STATUS_IN_PROGRESS => 'In progress',
        self::STATUS_READY_FOR_TEST => 'Ready for test',
    ];

    //Statuses a user with tester role is allowed to change tasks to.
    const TESTER_STATUSES = [
        self::STATUS_READY_FOR_TEST => 'Ready for test'
    ];

    //Statuses a user with product owner role is allowed for tasks to change the status.
    const PRODUCT_OWNER_CHANGE_STATUS_TO = [
        self::STATUS_IN_PROGRESS => 'In progress',
        self::STATUS_DONE => 'Done',
        self::STATUS_REJECTED => 'Rejected',
    ];
}
