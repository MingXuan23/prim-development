<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dorm extends Model
{
    //
    // use HasFactory;
    
    protected $fillable = ['name', 'accommodate_no', 'student_inside_no'];

    public $timestamps = false;
}
