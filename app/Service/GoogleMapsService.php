<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GoogleMapsService
{
    private $apiKey;
    private const CACHE_DURATION = 1800; // 30 دقيقة للتخزين المؤقت

    public function __construct()
    {
        $this->apiKey = 'AIzaSyDhjUlU89FeHPvu-urVVTckiGKW0rmm1D8';
    }

    public function getDistanceMatrix($origins, $destinations)
    {
        // تجهيز مصفوفة النقاط
        $originsStr = implode('|', array_map(function ($point) {
            return "{$point['lat']},{$point['lng']}";
        }, $origins));

        $destinationsStr = implode('|', array_map(function ($point) {
            return "{$point['lat']},{$point['lng']}";
        }, $destinations));

        // استخدام التخزين المؤقت لتوفير طلبات API
        $cacheKey = "distance_matrix_" . md5($originsStr . $destinationsStr);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($originsStr, $destinationsStr) {
            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => $originsStr,
                'destinations' => $destinationsStr,
                'mode' => 'driving',
                'departure_time' => 'now', // لحساب وقت الرحلة مع حركة المرور الحالية
                'traffic_model' => 'best_guess',
                'key' => $this->apiKey
            ]);

            if ($response->successful() && $response['status'] === 'OK') {
                return $response->json();
            }

            throw new \Exception('فشل في الحصول على بيانات المسافات من Google Maps');
        });
    }

    public function getRouteDetails($origin, $destination)
    {
        $cacheKey = "route_details_" . md5(json_encode($origin) . json_encode($destination));

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($origin, $destination) {
            $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
                'origin' => "{$origin['lat']},{$origin['lng']}",
                'destination' => "{$destination['lat']},{$destination['lng']}",
                'mode' => 'driving',
                'departure_time' => 'now',
                'alternatives' => 'true',
                'key' => $this->apiKey
            ]);

            if ($response->successful() && $response['status'] === 'OK') {
                return $response->json();
            }

            throw new \Exception('فشل في الحصول على تفاصيل المسار من Google Maps');
        });
    }
}