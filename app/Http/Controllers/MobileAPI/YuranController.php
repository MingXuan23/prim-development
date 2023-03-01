<?php

namespace App\Http\Controllers\MobileAPI;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YuranController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $username = $request->get('username');

        if(is_numeric($username)){
            
            if(!$this->startsWith((string)$username,"+60") && !$this->startsWith((string)$username,"60")){
                if(strlen((string)$username) == 10)
                {
                    $username = str_pad($username, 12, "+60", STR_PAD_LEFT);
                } 
                elseif(strlen((string)$username) == 11)
                {
                    $username = str_pad($username, 13, "+60", STR_PAD_LEFT);
                }   
            } else if($this->startsWith((string)$username,"60")){
                if(strlen((string)$username) == 11)
                {
                    $username = str_pad($username, 12, "+", STR_PAD_LEFT);
                } 
                elseif(strlen((string)$username) == 12)
                {
                    $username = str_pad($username, 13, "+", STR_PAD_LEFT);
                }   
            }
            
            $credentials = ['telno'=>$username, 'password' => $request->get('password')];

            if (Auth::attempt($credentials)) {
                $user = User::where('telno', $username)->first();
                return response($user->id, 200);
            }
        }
        else
        { 
            $credentials = ['email'=> $username, 'password' => $request->get('password')];

            if (Auth::attempt($credentials)) {
                $user = User::where('email', $username)->first();
                return response($user->id, 200);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    private function startsWith($string, $startString) {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}
