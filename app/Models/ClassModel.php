<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    //
    protected $table = 'classes';

    protected $fillable = ['nama', 'levelid'];

    public $timestamps = false;
}
