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
        'student_id',
        'teacher_id'
    ];

}
