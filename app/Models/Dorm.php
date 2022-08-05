<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dorm extends Model
{
    //
    protected $fillable = [
        'name',
        'accomodate_no',
        'student_inside_no'
    ];

    public $timestamps = false;
}
