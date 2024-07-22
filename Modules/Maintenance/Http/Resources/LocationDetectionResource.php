<?php

namespace Modules\Maintenance\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class locationDetectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'projects_name' => $this->projects_name,
            'client' => $this->client,
            'region' => $this->region,
            'city' => $this->city,
            'location_data' => $this->location_data,
            'elevator_data' => $this->elevator_data,
            'representatives' => $this->representatives,
            'elevatorType' => $this->elevator_type,
            'stopsNumbers' => $this->stops_number,
            'machineSpeed' => $this->machine_speed,
            'machineType' => $this->machine_type,
            'doorSize' => $this->door_size,
            'controlCard' => $this->control_card,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
