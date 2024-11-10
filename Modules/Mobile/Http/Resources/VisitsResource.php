<?php

namespace Modules\Mobile\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class VisitsResource extends JsonResource
{
    public function toArray($request)
    {
        $contract = $this->maintenanceContract;
        $client = $contract->client ?? null;
        $neighborhood = $contract->neighborhood ?? null;
        $area = $contract->area ?? null;
        $city = $contract->city ?? null;

        return [
            'id' => $this->id,
            'clientName' => $client ? $client->name : '',
            'clientPhone' => $this->formatPhoneNumber($client ? $client->phone : ''),
            'address' => $this->formatAddress($city, $neighborhood, $area, $contract),
            'date' => $this->created_at ? $this->created_at->format('d-m-Y') : '',
            'status' => $this->status ?? '',
            'notes' => $this->notes ?? '',
            'latitude' => $contract->latitude ?? null,
            'longitude' => $contract->longitude ?? null,
            'startTime' => $this->visit_start_date ?? null,
            'endTime' => $this->visit_end_date ?? null,
            'duration' => $this->calculateDuration(),
            'images' => $this->images ?? [],
            'test_checklist' => $this->test_checklist ?? [],
            'maintenanceItems' => $this->test_checklist ?? [],
        ];
    }


    private function formatAddress($city, $neighborhood, $area, $contract)
    {
        $parts = [
            $city ? $city->name : '',
            $neighborhood ? $neighborhood->name : '',
            $area ? $area->name : '',
            $contract->street ?? '',
            $contract->buildingNumber ?? ''
        ];

        return implode(', ', array_filter($parts));
    }

    private function formatPhoneNumber($phone)
    {
        // Remove any non-digit characters
        $digits = preg_replace('/\D/', '', $phone);

        // Take the last 9 digits
        $lastNine = substr($digits, -9);

        // Prefix with +996
        return '+996' . $lastNine;
    }

    private function calculateDuration()
    {
        if (!$this->visit_start_date) {
            return 0;
        }

        $startDate = $this->visit_start_date instanceof Carbon
            ? $this->visit_start_date
            : Carbon::parse($this->visit_start_date);

        if ($this->visit_end_date) {
            $endDate = $this->visit_end_date instanceof Carbon
                ? $this->visit_end_date
                : Carbon::parse($this->visit_end_date);
            return $endDate->diffInSeconds($startDate);
        }

        return now()->diffInSeconds($startDate);
    }
}