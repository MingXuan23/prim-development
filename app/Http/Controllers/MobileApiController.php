<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use IlluminateAgnostic\Arr\Support\Arr;
use Illuminate\Support\Facades\Validator;

class MobileApiController extends Controller
{
    public function getAllDonation()
    {
        $danoations = DB::table('donations as d')
            ->where('status', 1)
            ->get();
        return $danoations;
    }

    public function getAllDonationType()
    {
        $type = DB::table('donation_type as d')
            ->get();
        return $type;
    }

    public function getAllDonationQuantity()
    {
        $count = DB::table('donations as d')
            ->where('status', 1)  
            ->count();
        return $count;
    }

    public function getAllDonationTypeQuantity()
    {
        $count = DB::table('donation_type')
            ->count();
        return $count;
    }

    public function startsWith($string, $startString) {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }


    public function login(Request $request)
    {
        $phone = $request->get('username');
        if(is_numeric($request->get('username'))){
            
            if(!$this->startsWith((string)$request->get('username'),"+60") && !$this->startsWith((string)$request->get('username'),"60")){
                if(strlen((string)$request->get('username')) == 10)
                {
                    $phone = str_pad($request->get('username'), 12, "+60", STR_PAD_LEFT);
                } 
                elseif(strlen((string)$request->get('username')) == 11)
                {
                    $phone = str_pad($request->get('username'), 13, "+60", STR_PAD_LEFT);
                }   
            } else if($this->startsWith((string)$request->get('username'),"60")){
                if(strlen((string)$request->get('username')) == 11)
                {
                    $phone = str_pad($request->get('username'), 12, "+", STR_PAD_LEFT);
                } 
                elseif(strlen((string)$request->get('username')) == 12)
                {
                    $phone = str_pad($request->get('username'), 13, "+", STR_PAD_LEFT);
                }   
            }
            
            $credentials = ['telno'=>$phone, 'password' => $request->get('password')];

            if (Auth::attempt($credentials)) {
                $user = $request->user();
                return response($user, 200);
            }
        }
        else
        { 
            $credentials = ['email'=> $request->get('username'), 'password' => $request->get('password')];

            if (Auth::attempt($credentials)) {
                $user = $request->user();
                return response($user, 200);
            }
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }

    public function getAllStatistic()
    {
        $curYear = date("Y") . "-01-01";

        $organ = DB::table('organizations as o')
            ->count();
        
        $user = DB::table('users')
            ->count();
        
        $totalAmount = DB::table('transactions')
            ->where('status', 'success')
            ->where('nama', 'LIKE', 'donation%')
            ->where('datetime_created', '>', $curYear)
            ->select(DB::table('transactions')->raw('sum(amount) as total_amount'))
            ->first();
        
        $totalAmount = $totalAmount->total_amount;

        $transactions = DB::table('transactions')
            ->where('nama', 'LIKE', 'Donation%')
            ->where('status', 'Success')
            ->where('datetime_created', '>', $curYear)
            ->get()->count();
        

        $statistic = array(
            'organizationReport'  => $organ,
            'userReport'          => $user,
            'yearAmountReport'    => $totalAmount,
            'yearTransactionReport' => $transactions
        );

        return $statistic;
    }

    public function getallmytransactionhistory(Request $request)
    {
        $data = DB::table('transactions as t')
            ->leftJoin('donation_transaction as dt', 'dt.transaction_id', 't.id')
            ->leftJoin('donations as d', 'dt.donation_id', 'd.id')
            ->select('t.id', 't.datetime_created as row', 'd.nama', DB::raw('CONCAT("RM ", t.amount) as amount'))
            ->where('user_id', $request->userid)
            ->where('t.status', 'success')
            ->orderByDesc('datetime_created')
            ->get();

        foreach($data as $d)
        {
            $d->row = Carbon::createFromFormat('Y-m-d H:i:s', $d->row)->format('d/m');
        }
        
        return $data;
    }

    public function getlatesttransaction()
    {
        $data = DB::table('transactions as t')
            ->leftJoin('donation_transaction as dt', 'dt.transaction_id', 't.id')
            ->leftJoin('donations as d', 'dt.donation_id', 'd.id')
            ->select('t.id', 't.datetime_created as row', 'd.nama', DB::raw('CONCAT("RM ", t.amount) as amount'))
            ->where('t.status', 'success')
            ->orderByDesc('datetime_created')
            ->first();

        $data->row = Carbon::createFromFormat('Y-m-d H:i:s', $data->row)->format('d/m');

        return response()->json($data);
    }

    public function gettransactionbymonth()
    {
        $curYear = date("Y") . "-01-01";

        $donationbymonth = DB::table('transactions')
                        ->where('status', 'success')
                        ->where('nama', 'LIKE', 'donation%')
                        ->where('datetime_created', '>', $curYear)
                        ->select(DB::table('transactions')->raw('sum(amount) as value'))
                        ->groupBy(DB::table('transactions')->raw('DATE_FORMAT(datetime_created, "%m")'))
                        ->get();

        $month = [];

        for($i = 0; $i < 12; $i++)
        {
            if($i < count($donationbymonth))
            {
                array_push($month, ['label' => $i+1, 'value' => number_format($donationbymonth[$i]->value, 2)]);
            }
            else
            {
                array_push($month, ['label' => $i+1, 'value' => "0.00"]);
            }
        }

        return $month;
    }

    public function gettransactionbyyear()
    {
        return DB::table('transactions')
                ->where('status', 'success')
                ->where('nama', 'LIKE', 'donation%')
                ->select(DB::table('transactions')->raw('DATE_FORMAT(datetime_created, "%Y") as label'), DB::table('transactions')->raw('format(sum(amount), 2) as value'))
                ->groupBy('label')
                ->get();
    }

    public function donationnumberbyorganization()
    {
        return DB::table('organizations as o')
                ->leftJoin('donation_organization as do', 'do.organization_id', 'o.id')
                ->leftJoin('donations as d', 'd.id', 'do.donation_id')
                ->select('o.nama as label', DB::table('organizations')->raw('count("o.nama") as value'))
                ->groupBy('label')
                ->orderByDesc('value')
                ->where('d.status', 1)
                ->limit(10)
                ->get();
    }

    public function getdonationbycategory()
    {
        return DB::table('donation_type as dt')
                ->leftJoin('donations as d', 'd.donation_type', 'dt.id')
                ->select('dt.nama as label', DB::table('organizations')->raw('count("d.id") as value'))
                ->where('d.status', 1)
                ->groupBy('label')
                ->get();
    }

    public function updateProfile(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'telno'     => "required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,telno,$id",
            'email'     => "required|email|unique:users,email,$id",
        ]);

        if($validator->fails())
        {
            return response(401);
        }

        $userUpdate = DB::table('users')
            ->where('id', $id)
            ->update(
                [
                    'name'      => $request->post('name'),
                    'email'     => $request->post('email'),
                    'username'  => $request->post('username'),
                    'telno'     => $request->post('telno'),
                    'address'   => $request->post('address'),
                    'state'     => $request->post('state'),
                    'postcode'  => $request->post('postcode')
                ]
            );
        
        return response(200); 
    }
}
