<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class MobileAppAuth
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->input('user_token');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Missing Authorization Token'], 401);
        }

        $userTokenRecord = DB::table('user_token')
            ->where('api_token', $token)
            ->where('expired_at', '>', now())
            ->first();

        if (!$userTokenRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired or logged in elsewhere.',
                'error_code' => 'FORCE_LOGOUT'
            ], 401);
        }

        $requestDeviceToken = $request->input('device_token') ?? $request->header('device_token');
        if ($requestDeviceToken && $userTokenRecord->device_token !== $requestDeviceToken) {
            return response()->json([
                'success' => false,
                'message' => 'Device Mismatch',
                'error_code' => 'FORCE_LOGOUT'
            ], 401);
        }

        $request->merge(['auth_user_id' => $userTokenRecord->user_id]);

        return $next($request);
    }
}
