<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Save extends Model
{
    public $timestamps = false;

    protected $fillable = ["user_id", "post_id"];
}
