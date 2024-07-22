<?php

namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $contract = $this->assignment->contract;
        return [
            'id' => $this->id,
            'contract_number' => $contract->contract_number,
            'contract_id' => $contract->id,
            'client' => $contract->locationDetection->client,
            'elevatorType' => $contract->elevatorType->name,
            'stopNumbers' => $contract->stopsNumbers->name,
            'stageName' =>  $this->assignment->stage,
            'city' => $contract->locationDetection->city,
            'doorSize' => $contract->doorSize,
            'doorNumbers' => $contract->doors_number,
            'openingDirection' => $contract->outerDoorDirections,
            'neighborhood' => $contract->locationDetection->neighborhood,
            'financialStatus' => $this->assignment->financial_status,
            'representative' => $this->assignment->representative,
            'cost' => $contract->total,
            'status' => $this->assignment->status,
            'locationStatus' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
