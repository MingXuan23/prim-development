<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama', 'code', 'email', 'telno', 'address', 'postcode', 'state', 'type_org', 'fixed_charges', 'district', 'organization_picture'];

    // public $timestamps = false;

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsToMany(User::class, 'organization_user', 'organization_id', 'user_id');
    }

    public function donation()
    {
        return $this->belongsToMany(Donation::class, 'donation_organization');
    }

    public function donations()
    {
        return $this->hasManyThrough(Donation::class, DonationOrganization::class, 'organization_id', 'id', 'id', 'donation_id');
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function typeOrganization()
    {
        return $this->belongsTo('TypeOrganization');
    }

    public function getOrganizationByDonationId($donationId)
    {
        $organization = Organization::with(["donation"])->whereHas('donation', function ($query) use ($donationId) {
            $query->where("donations.id", $donationId);
        })->first();

        return $organization;
    }

    public function getOrganizationByType($type)
    {
        $organizations = Organization::where("type_org", $type)->get();
        return $organizations;
    }
}
