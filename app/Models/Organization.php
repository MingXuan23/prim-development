<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\OrganizationRole;

class Organization extends Model
{
    protected $fillable = ['nama', 'code', 'email', 'telno', 'address', 'postcode', 'state'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsToMany(User::class, 'organization_user');
    }
}
