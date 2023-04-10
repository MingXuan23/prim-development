<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['nama', 'description', 'organization_id'];

    public $timestamps = false;
}
