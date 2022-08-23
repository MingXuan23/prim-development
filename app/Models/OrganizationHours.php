<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationHours extends Model
{
    protected $fillable = ['day', 'open_hour', 'close_hour', 'status', 'organization_id'];
    public $timestamps = true;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
