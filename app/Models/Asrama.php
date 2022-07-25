<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asrama extends Model
{
    //
    protected $fillable = [
        'name', 
        'ic',
        'reason',
        'start_date',
        'end_date',
        'status',
    ];
}
