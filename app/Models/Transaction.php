<?php

namespace App\Models;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\DonationTransaction;

class Transaction extends Model
{
    public $timestamps = false;

    protected $dates = ['datetime_created'];

    // public function donation()
    // {
    //     return $this->hasOneThrough(Donation::class, DonationTransaction::class, 'transaction_id', 'id', 'id', 'donation_id');
    // }

    public function donation()
    {
        return $this->belongsToMany(Donation::class, 'donation_transaction', 'transaction_id', 'donation_id');
    }

    // public function fee()
    // {
    //     return $this->belongsToMany(Fee::class, 'fees_transactions', 'transactions_id', 'donation_id');
    // }


    public static function getTransactionByOrganizationIdAndStatus($organizationId)
    {
        $transaction = Transaction::select('*')
                        ->join('donation_transaction', 'transactions.id', '=', 'donation_transaction.transaction_id')
                        ->join('donations', 'donation_transaction.donation_id', '=', 'donations.id')
                        ->join('donation_organization', 'donations.id', '=', 'donation_organization.donation_id')
                        ->join('organizations', 'donation_organization.organization_id', '=', 'organizations.id')
                        ->where(([
                            ['organizations.id','=' ,$organizationId],
                            ['transactions.status', '=', 'Success']
                        ]));

        return $transaction;
    }

    public static function getTotalDonorByDay($organizationId)
    {
        $donors = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                    ->whereRaw('date(transactions.datetime_created) = curdate()')
                    ->select(DB::raw('count(transactions.id) as donor'))
                    ->first();

        return $donors;
    }

    public static function getTotalDonorByWeek($organizationId)
    {
        $donors = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                    ->whereRaw('YEARWEEK(transactions.datetime_created, 1) = YEARWEEK(CURDATE(), 1)')
                    ->select(DB::raw('count(transactions.id) as donor'))
                    ->first();

        return $donors;
    }

    public static function getTotalDonorByMonth($organizationId)
    {
        $donors = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                    ->whereRaw('year(transactions.datetime_created) = year(curdate())')
                    ->whereRaw('month(transactions.datetime_created) = month(curdate())')
                    ->select(DB::raw('count(transactions.id) as donor'))
                    ->first();

        return $donors;
    }

    public static function getTotalDonationByDay($organizationId)
    {
        $totalDonation = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                        ->whereRaw('date(transactions.datetime_created) = curdate()')
                        ->select(DB::raw('sum(transactions.amount) as donation_amount'))
                        ->first();

        return $totalDonation;
    }

    public static function getTotalDonationByWeek($organizationId)
    {
        $totalDonation = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                        ->whereRaw('YEARWEEK(transactions.datetime_created, 1) = YEARWEEK(CURDATE(), 1)')
                        ->select(DB::raw('sum(transactions.amount) as donation_amount'))
                        ->first();

        return $totalDonation;
    }

    public static function getTotalDonationByMonth($organizationId)
    {
        $totalDonation = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                        ->whereRaw('year(transactions.datetime_created) = year(curdate())')
                        ->whereRaw('month(transactions.datetime_created) = month(curdate())')
                        ->select(DB::raw('sum(transactions.amount) as donation_amount'))
                        ->first();

        return $totalDonation;
    }

    public static function getLastestTransaction($organizationId)
    {
        $result = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                ->select(['*',DB::raw('max(datetime_created) as latest')])
                ->groupBy('transactions.id')
                ->orderBy('latest', 'desc')
                ->take(4)
                ->get();

        return $result;
    }

    public static function getTransaction($organizationId)
    {
        $result = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                    ->get();

        return $result;
    }
}
