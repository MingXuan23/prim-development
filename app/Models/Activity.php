<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    //
    protected $fillable = ['name', 'description', 'date_created', 'date_start', 'date_end', 'status', 'organization_id'];

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    public function organization()
    {
        return $this->hasMany(Organization::class);
    }
}
