<?php

namespace Modules\Maintenance\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Maintenance\Http\Resources\MaintenanceVisitResource;

class MaintenanceContractResource extends JsonResource
{


    public function toArray($request)
    {

        return [
            'id'                     => $this->id,
            'contract_number'        => $this->contract_number,
            'area'                   => $this->area,
            'user_id'                => $this->user_id,
            'contract_type'          => $this->contract_type,
            'representatives'        => $this->representatives ?? null,
            'total'                  => $this->total,
            'city_id'                => $this->city_id,
            'neighborhood_id'        => $this->neighborhood_id,
            'latitude'               => $this->latitude,
            'longitude'              => $this->longitude,
            'client_id'              => $this->client_id,
            'elevator_type_id'       => $this->elevator_type_id,
            'building_type_id'       => $this->building_type_id,
            'stops_count'            => $this->stops_count,
            'has_window'             => $this->has_window,
            'has_stairs'             => $this->has_stairs,
            'visits_number'          => $this->visits_number,
            'site_images'            => $this->site_images ?? [],
            'logs'                   => $this->when($this->logs, $this->logs),
            'client'                 => $this->client,
            'elevatorType'           => $this->elevatorType,
            'machineType'            => $this->machineType,
            'contracts'              => $this->contractDetails,
            'active_contract'        => MaintenanceContractDetailResource::make($this->activeContract),
            'city'                   => $this->city,
            'neighborhood'           => $this->neighborhood,
            'region'                 => $this->region,
            'machineSpeed'           => $this->machineSpeed,
            'doorSize'               => $this->doorSize,
            'stopsNumber'            => $this->stopsNumber,
            'controlCard'            => $this->controlCard,
            'driveType'              => $this->driveType,
            'branch'                 => $this->branch,
            'buildingType'           => $this->buildingType,
            'status'                 => $this->status,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
}
