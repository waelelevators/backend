<?php

namespace Modules\Mobile\Http\Controllers;

use Illuminate\Routing\Controller;

class RouteOptimizerController extends Controller
{
    /**
     * حساب المسافة بين نقطتين باستخدام صيغة Haversine
     */
    private function calculateDistance($point1, $point2)
    {
        $R = 6371; // نصف قطر الأرض بالكيلومترات

        $lat1 = floatval($point1['lat']);
        $lon1 = floatval($point1['lng']);
        $lat2 = floatval($point2['lat']);
        $lon2 = floatval($point2['lng']);

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    /**
     * إيجاد أقرب المسارات باستخدام خوارزمية Nearest Neighbor
     */
    private function findNearestNeighborRoute($startPoint, $points)
    {
        $currentPoint = $startPoint;
        $unvisitedPoints = $points;
        $route = [$startPoint];
        $distances = [];

        while (!empty($unvisitedPoints)) {
            $nearestDistance = PHP_FLOAT_MAX;
            $nearestPoint = null;
            $nearestIndex = null;

            foreach ($unvisitedPoints as $index => $point) {
                $distance = $this->calculateDistance($currentPoint, $point);
                if ($distance < $nearestDistance) {
                    $nearestDistance = $distance;
                    $nearestPoint = $point;
                    $nearestIndex = $index;
                }
            }

            if ($nearestPoint) {
                $route[] = $nearestPoint;
                $distances[] = [
                    'from' => $currentPoint['label'] ?? 'نقطة غير معروفة',
                    'to' => $nearestPoint['label'] ?? 'نقطة غير معروفة',
                    'distance' => round($nearestDistance, 2)
                ];
                unset($unvisitedPoints[$nearestIndex]);
                $currentPoint = $nearestPoint;
            }
        }

        return [
            'route' => $route,
            'distances' => $distances
        ];
    }

    /**
     * API endpoint لحساب أفضل مسار
     */
    public function optimizeRoute()
    {




        $destinations = [
            [
                "lat" => 21.376586464293126,
                "lng" => 39.76652646931783,
                "label" => "نقطة البداية",
                "type" => "start"
            ],
            [
                "lat" => 21.38381958414226,
                "lng" => 39.772062548317216,
                "label" => "موقع 4",
                "type" => "waypoint"
            ],

            [
                "lat" => 21.531057675635555,
                "lng" => 39.17464553601162,
                "label" => "موقع 2",
                "type" => "waypoint"
            ],
            [
                "lat" => 21.375667312968602,
                "lng" => 39.781761415401405,
                "label" => "موقع 1",
                "type" => "waypoint"
            ],
            [
                "lat" => 21.38190144354417,
                "lng" => 39.793391472834216,
                "label" => "موقع 3",
                "type" => "waypoint"
            ]
        ];

        // إضافة label لنقطة البداية إذا لم تكن موجودة
        $startPoint['label'] = $startPoint['label'] ?? 'نقطة البداية';
        $startPoint['type'] = 'start';

        // تحسين المسار
        $result = $this->findNearestNeighborRoute([
            "lat" => 21.376586464293126,
            "lng" => 39.76652646931783,
            "label" => "نقطة البداية"
        ], $destinations);

        // تجهيز البيانات للرد
        $response = [
            'status' => 'success',
            'data' => [
                'optimizedRoute' => $result['route'],
                'routeDetails' => [
                    'totalPoints' => count($result['route']),
                    'segmentDistances' => $result['distances'],
                    'totalDistance' => array_sum(array_column($result['distances'], 'distance')),
                ],
                'metadata' => [
                    'startPoint' => $startPoint,
                    'timestamp' => now()->toIso8601String(),
                ]
            ]
        ];

        return response()->json($response);
    }
}
