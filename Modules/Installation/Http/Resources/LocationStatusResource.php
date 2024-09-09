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
            'client' => $contract->locationDetection->client->name,
            'phone' => $contract->locationDetection->client->phone,
            'elevatorType' => $contract->elevatorType->name,
            'stopNumbers' => $contract->stopsNumbers->name,
            'stageName' =>  $this->assignment->stage->name,
            'stageId' =>  $this->assignment->stage->id,
            'city' => $contract->locationDetection->city->name,
            'doorSize' => $contract->doorSize->name,
            'doorNumbers' => $contract->doors_number,
            'elevatorTrip' => $contract->elevatorTrip->name,
            'openingDirection' => $contract?->outerDoorDirections?->name,
            'neighborhood' => $contract->locationDetection->neighborhood->name,
            'financialStatus' => $contract->getRemainingAmountInStage($this->assignment->stage->id),
            'representative' => $this->assignment->representative->name,
            'cost' => $contract->total,
            'status' => $this->assignment->status,
            'locationStatus' => $this->status,
            'created_at' => $this->created_at,
        ];
    }

    public function transformData()
    {
        $contract = $this->assignment->contract;
        return [
            'id' => $this->id,
            'contract_id' => $contract->id,
            'contract_number' => $contract->contract_number,
            'client' => $contract->locationDetection->client->name,
            'phone' => $contract->locationDetection->client->phone,
            'elevatorType' => $contract->elevatorType->name,
            'stopNumbers' => $contract->stopsNumbers->name,
            'stageName' =>  $this->assignment->stage->name,
            'stageId' =>  $this->assignment->stage->id,
            'city' => $contract->locationDetection->city->name,
            'doorSize' => $contract->doorSize->name,
            'doorNumbers' => $contract->doors_number,
            'elevatorTrip' => $contract->elevatorTrip->name,
            'openingDirection' => $contract?->outerDoorDirections?->name,
            'neighborhood' => $contract->locationDetection->neighborhood->name,
            'financialStatus' => $this->assignment->financial_status,
            'representative' => $this->assignment->representative->name,
            'location_data' => $this->location_data,
            'machineType' => $contract->machineType->name,
            'machineSpeed' => $contract->machineSpeed->name,
            'controlCard' => $contract->controlCard->name,
            'cost' => $contract->total,
            'status' => $this->assignment->status,
            'notes' => $this->notes,
            'locationStatus' => $this->status,
            'created_at' => $this->created_at,

        ];
    }
}
