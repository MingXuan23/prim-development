<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class Grab_Student extends Model
{
    protected $table = 'grab_students';
    protected $primaryKey = 'id';
    protected $fillable = ['car_brand','car_name', 'car_registration_num', 'number_of_seat', 'available_time', 'status','id_organizations'];
    public $timestamps = false;

    
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function destination()
    {
        return $this->hasMany(Destination_Offer::class);
    }
}
