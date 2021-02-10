<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    //
    protected $fillable = ['nama', 'status', 'yearfees_id'];
    public $timestamps = false;
}
