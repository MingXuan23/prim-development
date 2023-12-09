<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassOrganization extends Model
{
    protected $table = 'class_organization';

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'class_organization', 'class_id', 'organization_id');
    }
}
