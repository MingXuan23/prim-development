<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $fillable = ['nama', 'icno', 'gender', 'email', 'parent_tel'];
    public $timestamps = false;
}
