<?php


namespace App\Service;

class EnhancedRouteOptimizationService
{
    private $googleMaps;
    private $locations;
    private $startPoint;

    public function __construct(GoogleMapsService $googleMaps)
    {
        $this->googleMaps = $googleMaps;
    }

    public function optimizeRoute(array $locations, array $startPoint)
    {
        $this->locations = array_values($locations);
        $this->startPoint = $startPoint;

        // تحويل الإحداثيات إلى التنسيق المطلوب
        $points = $this->prepareLocations();

        // الحصول على مصفوفة المسافات من Google Maps
        $distanceMatrix = $this->getDistanceMatrix($points);

        // إيجاد المسار الأمثل
        return $this->findOptimalRoute($distanceMatrix, $points);
    }

    private function prepareLocations()
    {
        $points = [
            [
                'lat' => $this->startPoint['latitude'],
                'lng' => $this->startPoint['longitude']
            ]
        ];

        foreach ($this->locations as $location) {
            $points[] = [
                'lat' => $location['latitude'],
                'lng' => $location['longitude']
            ];
        }

        return $points;
    }

    private function getDistanceMatrix($points)
    {
        return $this->googleMaps->getDistanceMatrix($points, $points);
    }

    private function findOptimalRoute($distanceMatrix, $points)
    {
        $currentPoint = 0; // نقطة البداية
        $unvisited = range(1, count($this->locations));
        $route = [];
        $totalDuration = 0;
        $totalDistance = 0;

        while (!empty($unvisited)) {
            // البحث عن أقرب نقطة زمنياً
            $nextPoint = null;
            $shortestDuration = PHP_FLOAT_MAX;

            foreach ($unvisited as $destinationIndex) {
                $element = $distanceMatrix['rows'][$currentPoint]['elements'][$destinationIndex];
                if ($element['status'] === 'OK') {
                    $duration = $element['duration_in_traffic']['value'] ?? $element['duration']['value'];
                    if ($duration < $shortestDuration) {
                        $shortestDuration = $duration;
                        $nextPoint = $destinationIndex;
                    }
                }
            }

            // الحصول على تفاصيل المسار المحدد
            $routeDetails = $this->googleMaps->getRouteDetails(
                $points[$currentPoint],
                $points[$nextPoint]
            );

            // إضافة النقطة للمسار
            $location = $this->locations[$nextPoint - 1];
            $element = $distanceMatrix['rows'][$currentPoint]['elements'][$nextPoint];

            $route[] = [
                'id' => $location['id'],
                'contract_number' => $location['contract_number'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'distance' => [
                    'text' => $element['distance']['text'],
                    'value' => $element['distance']['value']
                ],
                'duration' => [
                    'text' => $element['duration_in_traffic']['text'] ?? $element['duration']['text'],
                    'value' => $element['duration_in_traffic']['value'] ?? $element['duration']['value']
                ],
                // 'traffic_condition' => $this->getTrafficCondition($element),
                'route_info' => [
                    'polyline' => $routeDetails['routes'][0]['overview_polyline']['points'],
                    'steps_count' => count($routeDetails['routes'][0]['legs'][0]['steps']),
                    'has_tolls' => $this->checkForTolls($routeDetails['routes'][0])
                ]
            ];

            // تحديث الإجماليات
            $totalDistance += $element['distance']['value'];
            $totalDuration += $element['duration_in_traffic']['value'] ?? $element['duration']['value'];

            // تحديث الحالة
            $currentPoint = $nextPoint;
            $unvisited = array_diff($unvisited, [$nextPoint]);
        }

        // حساب مسار العودة إلى نقطة البداية
        $returnDetails = $this->calculateReturnRoute($points[$currentPoint], $points[0]);

        return [
            'start_point' => [
                'latitude' => $this->startPoint['latitude'],
                'longitude' => $this->startPoint['longitude']
            ],
            'optimized_route' => $route,
            'summary' => [
                'total_distance' => [
                    'text' => $this->formatDistance($totalDistance),
                    'value' => $totalDistance
                ],
                'total_duration' => [
                    'text' => $this->formatDuration($totalDuration),
                    'value' => $totalDuration
                ],
                'return_route' => $returnDetails,
                'locations_count' => count($route),
                'estimated_completion_time' => $this->estimateCompletionTime($totalDuration)
            ]
        ];
    }

    private function getTrafficCondition($element)
    {
        if (!isset($element['duration_in_traffic'])) {
            return 'unknown';
        }

        $normalDuration = $element['duration']['value'];
        $trafficDuration = $element['duration_in_traffic']['value'];
        $ratio = $trafficDuration / $normalDuration;

        if ($ratio <= 1.1) return 'low';
        if ($ratio <= 1.3) return 'moderate';
        if ($ratio <= 1.5) return 'heavy';
        return 'very_heavy';
    }

    private function checkForTolls($route)
    {
        return in_array('toll_road', $route['warnings'] ?? []);
    }

    private function calculateReturnRoute($lastPoint, $startPoint)
    {
        $routeDetails = $this->googleMaps->getRouteDetails($lastPoint, $startPoint);
        $leg = $routeDetails['routes'][0]['legs'][0];

        return [
            'distance' => [
                'text' => $leg['distance']['text'],
                'value' => $leg['distance']['value']
            ],
            'duration' => [
                'text' => $leg['duration_in_traffic']['text'] ?? $leg['duration']['text'],
                'value' => $leg['duration_in_traffic']['value'] ?? $leg['duration']['value']
            ],
            'traffic_condition' => isset($leg['duration_in_traffic']) ?
                $this->getTrafficCondition(['duration' => $leg['duration'], 'duration_in_traffic' => $leg['duration_in_traffic']]) :
                'unknown'
        ];
    }

    private function formatDistance($meters)
    {
        return $meters >= 1000 ?
            round($meters / 1000, 1) . ' كم' :
            $meters . ' متر';
    }

    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        $parts = [];
        if ($hours > 0) $parts[] = $hours . ' ساعة';
        if ($minutes > 0) $parts[] = $minutes . ' دقيقة';

        return implode(' و ', $parts);
    }

    private function estimateCompletionTime($totalDurationSeconds)
    {
        $averageStopDuration = 1800; // 30 دقيقة لكل توقف
        $totalSeconds = $totalDurationSeconds + (count($this->locations) * $averageStopDuration);

        $completionTime = now()->addSeconds($totalSeconds);

        return [
            'timestamp' => $completionTime->timestamp,
            'formatted' => $completionTime->format('Y-m-d H:i:s'),
            'human_readable' => $completionTime->diffForHumans()
        ];
    }
}