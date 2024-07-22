<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;


class MyHelper
{
    public static function greet($name)
    {
        return "Hello, $name!";
    }


    public static function pushNotification($interests, $data, $type = 'push')
    {
        $url = 'https://608af209-9cef-40c2-88dc-a42349540956.pushnotifications.pusher.com/publish_api/v1/instances/608af209-9cef-40c2-88dc-a42349540956/publishes';

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer 36A8DD71B43507278A2F6D4B32CD75679BD4FDD43F0D9BCD8517EA1B00B1E37D',
        ];

        $body = [
            'interests' => $interests,
            'web' => [
                'notification' => $data
            ]
        ];

        foreach ($interests as $email) {

            $user_id = User::where('email', $email)->first()->id;
            Notification::Create([
                'user_id' => $user_id,
                'data' => $data,
                'type' => $type,
            ]);
        }


        $response = Http::withHeaders($headers)->post($url, $body);

        return $response;
    }
}
