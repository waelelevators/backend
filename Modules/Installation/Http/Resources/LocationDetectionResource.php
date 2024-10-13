<?php

namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationDetectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'contract_number' => $this->contract_number ?? null,
            'project_name' => $this->project_name ?? null,
            'client' => $this->client ?? null,
            'region' => $this->region ?? null,
            'city' => $this->city ?? null,
            'location_data' => $this->location_data ?? null,
            'is_there_window' => $this->elevator_data['is_there_window'] ?? 'unkown',
            'is_there_stair' => $this->elevator_data['is_there_stair'] ?? 'unkown',
            'representatives' => $this->representatives ?? null,
            'buildingType' => $this->building_type ?? [],
            'elevatorType' => $this->elevator_type ?? [],
            'elevator_type_id' => $this->elevator_data['elevator_type_id'] ?? null,
            'stopsNumbers' => $this->stops_number ?? null,
            'machineSpeed' => $this->machine_speed ?? null,
            'machineType' => $this->machine_type ?? null,
            'doorSize' => $this->door_size ?? null,
            'controlCard' => $this->control_card ?? null,
            'activeContract' => $this->active_contract ?? null,
            'contracts' => $this->contracts ?? null,
            'created_at' => $this->created_at->format('Y-m-d') ?? null,
        ];
    }
}