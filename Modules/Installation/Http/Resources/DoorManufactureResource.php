<?php

namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DoorManufactureResource extends JsonResource
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
            'contract_id' => $this->contract->id,
            'contract_number' => $this->contract->contract_number,
            'city' => $this->contract->locationDetection->city,
            'neighborhood' => $this->contract->locationDetection->neighborhood,
            'client' => $this->contract->locationDetection->client,
            'doorCover' => $this->doorCover,
            'doorSize' => $this->doorSize,
            'elevatorType' => $this->contract->elevatorType,
            'doors_number' => $this->doors_number,
            'notes' => $this->notes,
            'status' => $this->status,
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
