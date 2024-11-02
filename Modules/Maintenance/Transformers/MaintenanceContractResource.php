<?php

namespace Modules\Maintenance\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Maintenance\Http\Resources\MaintenanceVisitResource;

class MaintenanceContractResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'contract_number' => $this->contract_number,
            'user_id' => $this->user_id,
            'contract_type' => $this->contract_type,
            'region' => $this->region,
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'area' => $this->area,
            'total' => $this->total,
            'region_id' => $this->region_id,
            'city_id' => $this->city_id,
            'neighborhood_id' => $this->neighborhood_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'client_id' => $this->client_id,
            'elevator_type_id' => $this->elevator_type_id,
            'building_type_id' => $this->building_type_id,
            'stops_count' => $this->stops_count,
            'has_window' => $this->has_window,
            'has_stairs' => $this->has_stairs,
            'visits_number' => $this->visits_number,
            'site_images' => $this->site_images ?? [],
            'logs' => $this->when($this->logs, $this->logs),
            'client' => $this->client,
            'elevatorType' => $this->elevatorType,
            'machineType' =>  $this->machineType,
            'contracts' => $this->contractDetails,
            'active_contract' =>  MaintenanceContractDetailResource::make($this->activeContract),
            'machineSpeed' =>  $this->machineSpeed,
            'stopCount' => $this->stopCount,
            'controlCard' => $this->controlCard,
            'driveType' => $this->driveType,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
