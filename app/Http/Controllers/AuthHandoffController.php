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
            "redirect_url" => "required",
        ]);

        $user = DB::table("user_token")
            ->where('remember_token', '=', $request->header('user_token'))
            ->first();

        if ($user == null) {
            abort(404, "User not found with token.");
        }

        $token = Str::random(64);

        $cacheKey = 'handoff_token:' . $token;

        $payload = [
            "user_id" => $user->id,
            "redirect_url" => $request->get('redirect_url')
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

        $url = $payload['redirect_url'];

        return redirect("sumbangan/$url");
    }
}
