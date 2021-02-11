<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    //

    protected $fillable = ['nama','email', 'telno', 'address', 'postcode', 'state'];

    public $timestamps = false;
}
