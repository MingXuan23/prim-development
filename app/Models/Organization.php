<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama', 'code', 'email', 'telno', 'address', 'postcode', 'state', 'type_org', 'min_waive_service_charge_amount', 'fixed_charges', 'district', 'city', 'organization_picture', 'parent_org'];

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

    public function organization_hours()
    {
        return $this->hasMany(OrganizationHours::class);
    }

    public function product_item()
    {
        return $this->hasMany(ProductItem::class);
    }

    public function pgng_orders(){
        return $this->hasMany(PgngOrder::class);
    }

    public function pickup_order()
    {
        return $this->hasMany(PickUpOrder::class);
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
        if ($type == 8) {
            $organizations = Organization::where('organizations.nama', 'like', '%'.'UNIVERSITI'.'%');
        } else {
            $organizations = Organization::where("type_org", $type)->get();
        }
        return $organizations;
    }

    public function promotion()
    {
        return $this->hasMany(Promotion::class);
    }

    public function room()
    {
        return $this->hasMany(Room::class , 'id' ,'homestayid')->where('deleted',null);
    }

    public function grab()
    {
        return $this->hasMany(Grab_Student::class);
    }

    public function bus()
    {
        return $this->hasMany(Bus::class);
    }

    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'class_organization', 'organization_id', 'class_id')
        ->orderBy('nama');;
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function charges(){
        return $this->hasMany(OrganizationCharge::class);
    }
}
