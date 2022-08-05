<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outing extends Model
{
    //
    protected $fillable = ['start_date_time', 'end_date_time'];

    public $timestamps = false;
}
