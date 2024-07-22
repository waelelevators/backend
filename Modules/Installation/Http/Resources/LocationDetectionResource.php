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
            'contract_number' => $this->contract_number,
            'project_name' => $this->project_name,
            'client' => $this->client,
            'region' => $this->region,
            'city' => $this->city,
            'location_data' => $this->location_data,
            'is_there_window' => $this->elevator_data['is_there_window'] ?? 'unkown',
            'is_there_stair' => $this->elevator_data['is_there_stair'] ?? 'unkown',
            'representatives' => $this->representatives,
            'buildingType' => $this->building_type,
            'elevatorType' => $this->elevator_type,
            'elevator_type_id' => $this->elevator_data['elevator_type_id'],
            'stopsNumbers' => $this->stops_number,
            'machineSpeed' => $this->machine_speed,
            'machineType' => $this->machine_type,
            'doorSize' => $this->door_size,
            'controlCard' => $this->control_card,
            'activeContract' => $this->active_contract,
            'contracts' => $this->contracts,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
