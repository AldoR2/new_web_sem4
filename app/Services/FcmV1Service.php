<?php

namespace App\Services;

use Google_Client;
use GuzzleHttp\Client;

class FcmV1Service
{
    protected $messaging;


    public function send($fcmToken, $title, $body)
    {
        // 🔐 Ambil akses token dari service account
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/firebase/stipres-flutter-firebase-adminsdk-fbsvc-a6ee09de92.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithAssertion();
        }

        $accessToken = $client->getAccessToken()['access_token'];

        // 🔧 Ganti dengan Project ID kamu
        $projectId = 'stipres-flutter';
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // 🧾 JSON payload untuk Firebase v1
        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                    ]
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ]
        ];

        // ✅ DEBUG: log json payload ke laravel.log
        \Log::info('🔧 FCM Payload:', $payload);

        // 🔥 Kirim request ke Firebase HTTP v1
        $client = new Client();
        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody(), true);
            \Log::info('✅ FCM response: ', $result);

            return $result;
        } catch (\Exception $e) {
            \Log::error('❌ FCM Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
