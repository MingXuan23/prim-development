<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class Donation extends Model
{
    use SoftDeletes;

    public $table = "donations";
    protected $fillable = ['nama', 'description', 'date_created', 'date_started', 'date_end', 'status', 'url', 'tax_payer', 'total_tax', 'donation_poster', 'donation_type', 'lhdn_reference_code'];
    
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

    public function getDonationByTransactionName($transaction_name)
    {
        $donation = Donation::with(["transaction"])->whereHas('transaction', function ($query) use ($transaction_name) {
            $query->where("nama", $transaction_name);
        })->first();

        // $donation = DB::table('donations')
        //             ->leftJoin('donation_transaction', "donations.id", "donation_transaction.donation_id")
        //             ->leftJoin("transactions", "transactions.id", "=", "donation_transaction.transaction_id")
        //             ->where("transactions.nama", $transaction_name)
        //             ->select('donations.*')
        //             ->first();

        return $donation;
    }

    public function getDonationByOrganizationId($id)
    {
        $donation = Donation::with(["organization"])->whereHas('organization', function ($query) use ($id) {
            $query->where("organizations.id", $id);
        })->get();

        return $donation;
    }

}
