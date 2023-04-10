<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;

class OrganizationRole extends Model
{
    protected $table = "organization_roles";

    public function user()
    {
        return $this->belongsToMany(User::class, 'organization_user');
    }
}
