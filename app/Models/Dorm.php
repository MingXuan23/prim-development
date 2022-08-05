<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dorm extends Model
{
    //
    protected $fillable = ['name', 'accommodate_no', 'student_inside_no'];

    public $timestamps = false;
}
