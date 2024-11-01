<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractQuotationsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'region' => $this->region,
            'city' => $this->city,
            'client' => $this->client,
            'neighboor_hood' => $this->neighboor_hood,
            'total_price' => $this->total_price,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'elevator_type' => $this->elevator_type,
            'stops_number' => $this->stops_number,
            'elevator_trip' => $this->elevator_trip,
            'machine_load' => $this->machine_load,
            'people_load' => $this->people_load,
            'control_card' => $this->control_card,
            'entrances_number' => $this->entrances_number,
            'door_size' => $this->door_size,
            'machine_type' => $this->machine_type,
            'machine_speed' => $this->machine_speed,
            'elevator_warranties' => $this->elevator_warranties,
            'drive_type' => $this->drive_type,
            'user' => $this->user,
        ];
    }
}
