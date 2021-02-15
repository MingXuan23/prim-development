<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = ['nama', 'description', 'amount', 'date_created', 'status'];
    public $timestamps = false;

    public function organization()
    {
        return $this->belongsToMany(Organization::class, 'donation_organization');
    }
}
