<?php

namespace App\Models;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Model;
use DB;

class Transaction extends Model
{
    public $timestamps = false;

    protected $dates = ['datetime_created'];

    public function donation()
    {
        return $this->hasOneThrough(Donation::class, DonationTransaction::class, 'transaction_id', 'id', 'id', 'donation_id');
    }

    public static function getTransactionByOrganizationIdAndStatus($organizationId)
    {
        $transaction = Transaction::select(DB::raw('sum(transactions.amount) as donation_amount'))
                        ->join('donation_transaction', 'donation_transaction.transaction_id', '=', 'transactions.id')
                        ->join('donations', 'donation_transaction.donation_id', '=', 'donations.id')
                        ->join('donation_organization', 'donation_organization.donation_id', '=', 'donations.id')
                        ->join('organizations', 'organizations.id', '=', 'donation_organization.organization_id')
                        ->where(([
                            ['organizations.id','=' ,$organizationId],
                            ['transactions.status', '=', 'Success']
                        ]));

        return $transaction;
    }
}
