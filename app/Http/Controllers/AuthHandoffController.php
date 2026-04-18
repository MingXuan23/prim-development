<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use IlluminateAgnostic\Collection\Support\Str;

class AuthHandoffController extends Controller
{
    public function getHandoffToken(Request $request)
    {
        $request->validate([
            "donation_id" => "required",
            "user_token" => "required",
            "desc" => "required"
        ]);

        $userToken = DB::table("user_token")
            ->where('remember_token', '=', $request->get('user_token'))
            ->first();

        if ($userToken == null) {
            abort(404, "User not found with token.");
        }

        $randomStr = Str::random(64);

        $token = 'handoff_token_' . $randomStr . $userToken->user_id;

        $payload = [
            "user_id" => $userToken->user_id,
            "donation_id" => $request->get('donation_id'),
            "desc" => $request->get("desc")
        ];

        Cache::put($token, $payload, 60);

        return response()->json(['status' => 'success', 'token' => $token]);
    }

    public function useHandoffToken(Request $request)
    {
        $payload = Cache::pull($request->query('token'));

        if ($payload == null) {
            abort(403, "The token is invalid or expired.");
        }

        Auth::loginUsingId($payload["user_id"]);

        $donation = DB::table("donations")
            ->where("id", "=", $payload["donation_id"])
            ->first();

        if ($payload["desc"] == "Derma Tanpa Nama") {
            return redirect()->route('ANONdonate', ['link' => $donation->url]);
        } else {
            return redirect()->route('URLdonate', ['link' => $donation->url]);
        }
    }
}
