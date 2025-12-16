<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class MobileAppAuth
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            $token = $request->input('user_token');
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Missing Authorization Token'
            ], 401);
        }

        $userTokenRecord = DB::table('user_token')
            ->where('api_token', $token)
            ->where('expired_at', '>', now())
            ->first();

        if (!$userTokenRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or Expired Token'
            ], 401);
        }

        $request->merge(['auth_user_id' => $userTokenRecord->user_id]);

        return $next($request);
    }
}
