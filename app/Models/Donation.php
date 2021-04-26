<?php

namespace App\Models;

use App\User;
use DonationTransactionTableSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class Donation extends Model
{
    use SoftDeletes;

    public $table = "donations";
    protected $fillable = ['nama', 'description', 'date_created', 'date_started', 'date_end', 'status', 'url'];
    
    public $timestamps = false;

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsToMany(User::class, 'donation_user');
    }

    public function reminder()
    {
        return $this->hasMany(Reminder::class);
    }

    public function transaction()
    {
        return $this->belongsToMany(Transaction::class, 'donation_transaction', 'donation_id', 'transaction_id');
    }

    // public function transactions()
    // {
    //     return $this->hasManyThrough(Transaction::class, DonationTransaction::class, 'donation_id', 'id', 'id', 'transaction_id');
    // }

    public function organization()
    {
        return $this->belongsToMany(Organization::class, 'donation_organization');
    }

    public function getUrl()
    {
        $cat = 'ss';
        $id = 'kayu';
        return URL::action('DonationController@urlDonation', array('id' => $cat));
    }

    public function getAllDonation()
    {
        $donations = Donation::all();

        return $donations;
    }

    public function getDonationByReminderId($id)
    {
        $donations = Donation::with(["reminder"])->whereHas('reminder', function ($query) use ($id) {
            $query->where("id", $id);
        })->get();

        return $donations;
    }

    public function getDonationById($id)
    {
        $donation = Donation::find($id);
        
        return $donation;
    }
}
