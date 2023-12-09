<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
    protected $table = 'schedules';
    // protected $fillable = [
    //     'organization_id',
    //     'class_id',
    //     'day_of_week',
    //     'slot_1',
    //     'slot_2',
    //     'slot_3',
    //     'slot_4',
    //     'slot_5',
    //     'slot_6',
    //     'slot_7',
    //     'slot_8',
    //     'slot_9',
    //     'slot_10',
    //     'slot_11',
    //     'slot_12',
    // ];

    protected $fillable = [
        'name',
        'number_of_slot',
        'time_of_slot',
        'start_time',
        'day_of_week',
        // 'time_off',
        'target',
        'status',
        'teacher_max_slot',
        'organization_id',
    ];

    public $timestamps = false;
}
