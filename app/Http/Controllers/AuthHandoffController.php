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

        $user = DB::table("user_token")
            ->where('remember_token', '=', $request->get('user_token'))
            ->first();

        if ($user == null) {
            abort(404, "User not found with token.");
        }

        $token = Str::random(64);

        $cacheKey = 'handoff_token:' . $token;

        $payload = [
            "user_id" => $user->id,
            "donation_id" => $request->get('donation_id'),
            "desc" => $request->get("desc")
        ];

        Cache::put($cacheKey, $payload, 60);

        return response()->json(['status' => 'success', 'token' => $token]);
    }

    public function useHandoffToken(Request $request)
    {
        $cacheKey = 'handoff_token:' . $request->query('token');
        $payload = Cache::pull($cacheKey);

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
