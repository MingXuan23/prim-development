<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asrama extends Model
{
    //
    protected $fillable = [
        'reason',
        'start_date',
        'end_date',
        'status',
        'outing_time',
        'out_arrive_time',
        'in_time',
        'in_arrive_time',
        'student_id',
        'teacher_id'
    ];

    public $timestamps = false;

}
