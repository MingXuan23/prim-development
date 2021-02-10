<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    //
    protected $table = 'users';

    protected $fillable = ['name', 'icno', 'email', 'password', 'telno', 'remember_token'];

}
