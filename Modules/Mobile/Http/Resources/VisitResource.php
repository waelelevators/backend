<?php

namespace Modules\Mobile\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
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
            'maintenanceContract' => $this->maintenanceContract,
            'clientName' => $client ? $client->name : '',
            'clientPhone' => $this->formatPhoneNumber($client ? $client->phone : ''),
            'address' => $this->formatAddress($city, $neighborhood, $area, $contract),
            'visitDate' => $this->created_at ? $this->created_at->format('d.m.Y') : '',
            'status' => $this->status ?? '',
            'latitude' => $contract->latitude ?? null,
            'longitude' => $contract->longitude ?? null,
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
}