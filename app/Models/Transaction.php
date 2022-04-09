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

    public function donation()
    {
        return $this->belongsToMany(Donation::class, 'donation_transaction', 'transaction_id', 'donation_id');
    }

    public static function getTransactionByOrganizationIdAndStatus($organizationId)
    {
        $transaction = Transaction::select('*')
            ->leftjoin('donation_transaction', 'transactions.id', '=', 'donation_transaction.transaction_id')
            ->leftjoin('donations', 'donation_transaction.donation_id', '=', 'donations.id')
            ->leftjoin('donation_organization', 'donations.id', '=', 'donation_organization.id')
            ->leftjoin('organizations', 'donation_organization.organization_id', '=', 'organizations.id')
            ->where(([
                ['donations.id', '=', $organizationId],
                ['transactions.status', '=', 'Success']
            ]));

        return $transaction;
    }

    public static function getTotalDonorByDay($organizationId)
    {
        $donors = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
            ->whereRaw('date(transactions.datetime_created) = curdate()')
            ->select(DB::raw('count(donation_transaction.donation_id) as donor'))
            ->first();

        return $donors;
    }

    public static function getTotalDonorByWeek($organizationId)
    {
        $donors = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
            ->whereRaw('YEARWEEK(transactions.datetime_created, 1) = YEARWEEK(CURDATE(), 1)')
            ->select(DB::raw('count(donation_transaction.donation_id) as donor'))
            ->first();

        return $donors;
    }

    public static function getTotalDonorByMonth($organizationId)
    {
        $donors = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
            ->whereRaw('year(transactions.datetime_created) = year(curdate())')
            ->whereRaw('month(transactions.datetime_created) = month(curdate())')
            ->select(DB::raw('count(donation_transaction.donation_id) as donor'))
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
            ->select(['*', DB::raw('max(datetime_created) as latest')])
            ->where('transactions.status', '=', 'Success')
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

    public function getTransactionByName($name)
    {
        $transaction = Transaction::where("nama", $name)->first();
        return $transaction;
    }

    public function getDonorByDonationId($id)
    {
        $donor = Transaction::with(["donation"])->whereHas('donation', function ($query) use ($id) {
            $query->where("donations.id", $id);
        })->get();

        return $donor;
    }

    // *************************** fees ***************************

    public static function getLastestTransaction_fees($organizationId)
    {
        $result = DB::table('transactions')
            ->join('fees_transactions', 'fees_transactions.transactions_id', '=', 'transactions.id')
            ->join('student_fees', 'student_fees.id', '=', 'fees_transactions.student_fees_id')
            ->join('fees_details', 'fees_details.id', '=', 'student_fees.fees_details_id')
            ->join('fees', 'fees.id', '=', 'fees_details.fees_id')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_fees.class_organization_id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->select(['transactions.*', DB::raw('max(transactions.datetime_created) as latest')])
            ->where(([
                ['organizations.id', '=', $organizationId],
                ['transactions.status', '=', 'Success']
            ]))
            ->groupBy('transactions.id')
            ->orderBy('latest', 'desc')
            ->take(5)
            ->get();

        // dd($result);
        return $result;
    }

    public static function getTransaction_fees($organizationId)
    {
        $result = Transaction::getTransactionByOrganizationIdAndStatus_fees($organizationId)
            ->get();

        return $result;
    }

    public static function getTransactionByOrganizationIdAndStatus_fees($organizationId)
    {
        $transaction = DB::table('transactions')
            ->join('fees_transactions', 'fees_transactions.transactions_id', '=', 'transactions.id')
            ->join('student_fees', 'student_fees.id', '=', 'fees_transactions.student_fees_id')
            ->join('fees_details', 'fees_details.id', '=', 'student_fees.fees_details_id')
            ->join('fees', 'fees.id', '=', 'fees_details.fees_id')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_fees.class_organization_id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->select('transactions.*')
            ->where(([
                ['organizations.id', '=', $organizationId],
                ['transactions.status', '=', 'Success']
            ]));

        // dd($transaction);
        return $transaction;
    }

    // ******************* Category A


    public static function getTransactionByCat_fees($organizationId)
    {

        $transaction = DB::table('transactions')
            ->join('fees_transactions', 'fees_transactions.transactions_id', '=', 'transactions.id')
            ->join('student_fees', 'student_fees.id', '=', 'fees_transactions.student_fees_id')
            ->join('fees_details', 'fees_details.id', '=', 'student_fees.fees_details_id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->join('fees', 'fees.id', '=', 'fees_details.fees_id')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_fees.class_organization_id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->select('transactions.*')
            ->where(([
                ['organizations.id', '=', $organizationId],
                ['transactions.status', '=', 'Success']
            ]));

        return $transaction;
    }

    public static function getTotalDonorByDay_CatA($organizationId)
    {
        $donors = Transaction::getTransactionByCat_fees($organizationId)
            ->whereRaw('date(transactions.datetime_created) = curdate()')
            ->select(DB::raw('count(transactions.id) as donor'))
            ->first();
        
        return response()->json($donors);
    }


    public static function getTotalDonorByWeek_CatA($organizationId)
    {
        $donors = Transaction::getTransactionByCat_fees($organizationId)
            ->whereRaw('YEARWEEK(transactions.datetime_created, 1) = YEARWEEK(CURDATE(), 1)')
            ->select(DB::raw('count(transactions.id) as donor'))
            ->first();

            return response()->json($donors);
    }

    public static function getTotalDonorByMonth_CatA($organizationId)
    {
        $donors = Transaction::getTransactionByCat_fees($organizationId)
            ->whereRaw('year(transactions.datetime_created) = year(curdate())')
            ->whereRaw('month(transactions.datetime_created) = month(curdate())')
            ->select(DB::raw('count(transactions.id) as donor'))
            ->first();

            return response()->json($donors);
    }

    // ******************* Category B

    public static function getTotalDonationByDay_CatB($organizationId)
    {
        $totalDonation = Transaction::getTransactionByCat_fees($organizationId)
            ->whereRaw('date(transactions.datetime_created) = curdate()')
            ->select(DB::raw('sum(transactions.amount) as donation_amount'))
            ->first();

        return response()->json($totalDonation);
    }

    public static function getTotalDonationByWeek_CatB($organizationId)
    {
        $totalDonation = Transaction::getTransactionByCat_fees($organizationId)
            ->whereRaw('YEARWEEK(transactions.datetime_created, 1) = YEARWEEK(CURDATE(), 1)')
            ->select(DB::raw('sum(transactions.amount) as donation_amount'))
            ->first();

        return response()->json($totalDonation);
    }

    public static function getTotalDonationByMonth_CatB($organizationId)
    {
        $totalDonation = Transaction::getTransactionByCat_fees($organizationId)
            ->whereRaw('year(transactions.datetime_created) = year(curdate())')
            ->whereRaw('month(transactions.datetime_created) = month(curdate())')
            ->select(DB::raw('sum(transactions.amount) as donation_amount'))
            ->first();

        return response()->json($totalDonation);
    }
}
