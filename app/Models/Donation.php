<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    protected $fillable = ['nama', 'description', 'date_created', 'date_started', 'date_end', 'status'];
    
    public $timestamps = false;

    protected $dates = ['deleted_at'];

    public function organization()
    {
        return $this->belongsToMany(Organization::class, 'donation_organization');
    }
}
