<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Facades\Auth;

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
}
