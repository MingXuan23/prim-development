<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee_New extends Model
{
    //
    protected $casts = [
        'target' => 'array'
    ];
}
