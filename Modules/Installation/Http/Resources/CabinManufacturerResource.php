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
            'client' => $this->contract->locationDetection->client,
            'contract_number' => $this->contract->contract_number,
            'city' => $this->contract->locationDetection->city,
            'neighborhood' => $this->contract->locationDetection->neighborhood,
            'notes' => $this->notes,
            'status' => $this->status,
            'elevatorType' => $this->contract->elevatorType,
            'stopsNumber' => $this->contract->stopsNumbers,
            'weight_dbg' => $this->weight_dbg,
            'cabin_dbg' => $this->cabin_dbg,
            'weightLocation' => $this->weightLocation,
            'doorSize' => $this->doorSize,
            'machine_chair' => $this->machine_chair,
            'doorType' => $this->doorType,
            'doorDirection' => $this->doorDirection,
            'coverType' => $this->coverType,
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
