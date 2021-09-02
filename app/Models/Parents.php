<?php

namespace App\Models;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    protected $table = 'users';

    protected $fillable = ['name', 'icno', 'email', 'password', 'telno', 'remember_token'];
}
