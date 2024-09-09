<?php

namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CabinManufacturerResource extends JsonResource
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
            'client_name' => $this->contract->locationDetection->client->name,
            'client_phone' => $this->contract->locationDetection->client->phone,
            'contract_number' => $this->contract->contract_number,
            'city' => $this->contract->locationDetection->city->name,
            'neighborhood' => $this->contract->locationDetection->neighborhood->name,
            'notes' => $this->notes,
            'statusId' => $this->status->id,
            'statusName' => $this->status->name,
            'statusColor' => $this->status->color,
            'elevator_type' => $this->contract->elevatorType->name,
            'stopsNumber' => $this->contract->stopsNumbers->name,
            'weight_dbg' => $this->weight_dbg,
            'cabin_dbg' => $this->cabin_dbg,
            'weightLocation' => $this->weightLocation->name,
            'doorSize' => $this?->door_size,
            'machine_chair' => $this->machine_chair,
            'doorType' => $this?->doorType?->name,
            'doorDirection' => $this?->doorDirection?->name,
            'coverType' => $this?->coverType?->name,
            'machine_room_height' => $this->machine_room_height,
            'machine_room_width' => $this->machine_room_width,
            'machine_room_depth' => $this->machine_room_depth,
            'cabin_max_height' => $this->cabin_max_height,
            'last_floor_height' => $this->last_floor_height,
            'order_attached' => $this->order_attached,
            'started_date' => $this->started_date,
            'accept_date' => $this->orderResponse->accept_time ?? '',
            'ended_date' => $this->orderResponse->ended_time ?? '',
            'accepted_by' => $this->orderResponse->acceptedBy->name ?? '',
            'ended_by' => $this->orderResponse->endedBy->name ?? '',
            'user' => $this->user->name ?? '',
        ];
    }
}
