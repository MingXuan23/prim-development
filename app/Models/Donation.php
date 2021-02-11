<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = ['nama', 'description', 'amount', 'date_created', 'status'];
    public $timestamps = false;
}
