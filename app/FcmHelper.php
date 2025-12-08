<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FcmHelper
{
    public static function sendToUser($userId, $title, $body, $data = [])
    {

        $userToken = DB::table('user_token')
            ->where('user_id', $userId)
            ->where('application_id', 2)
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$userToken || !$userToken->fcm_token) {
            return false;
        }

        $deviceToken = $userToken->fcm_token;

        return self::sendByToken($deviceToken, $title, $body, $data);
    }

    public static function sendByToken($token, $title, $body, $data = [])
    {
        $serviceAccountPath = storage_path('app/firebase/service-account.json');

        if (!file_exists($serviceAccountPath)) {
            return false;
        }

        $credentials = json_decode(file_get_contents($serviceAccountPath), true);

        $clientEmail = $credentials['client_email'];
        $privateKey = $credentials['private_key'];

        $now = time();
        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtPayload = base64_encode(json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]));

        $input = $jwtHeader . '.' . $jwtPayload;
        openssl_sign($input, $signature, $privateKey, 'SHA256');
        $signedJwt = $input . '.' . base64_encode($signature);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $signedJwt
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $responseData = json_decode($response, true);
            return false;
        }

        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'];

        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'data' => array_merge([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ], $data),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'fcm_default_channel',
                        'sound' => 'default'
                    ]
                ]
            ]
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/prim-mobileyuran-notification/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($result, true);

        if ($httpCode === 200) {
            return true;
        } else {
            return false;
        }
    }
}
