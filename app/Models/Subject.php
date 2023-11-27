<?php

namespace App\models;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    //
    protected $table = 'subject';

    protected $fillable = ['nama', 'code'];
}
