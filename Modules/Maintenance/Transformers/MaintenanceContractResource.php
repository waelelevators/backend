<?php

namespace Modules\Maintenance\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceContractResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'contract_number' => $this->contract_number,
            'area' => $this->area,
            'user_id' => $this->user_id,
            'contract_type' => $this->contract_type,
            'total' => $this->total,
            'city_id' => $this->city_id,
            'neighborhood_id' => $this->neighborhood_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'customer_id' => $this->customer_id,
            'elevator_type_id' => $this->elevator_type_id,
            'building_type_id' => $this->building_type_id,
            'stops_count' => $this->stops_count,
            'has_window' => $this->has_window,
            'has_stairs' => $this->has_stairs,
            'site_images' => $this->site_images,
            'logs' => $this->logs,
            'client' => $this->client,

            'elevatorType' => $this->elevatorType,
            'machineType' => $this->machineType,
            'contracts' => new MaintenanceContractDetailResource($this->contractDetail),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
