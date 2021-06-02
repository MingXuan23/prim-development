<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeOrganization extends Model
{
    public $timestamps = false;

    public function teacher()
    {
        return $this->hasMany('Organization');
    }
}
