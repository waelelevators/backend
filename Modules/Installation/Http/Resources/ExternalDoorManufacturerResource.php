<?php

namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExternalDoorManufacturerResource extends JsonResource
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
            'client' => $this->contract->locationDetection->client,
            'city' => $this->contract->locationDetection->city,
            'neighborhood' => $this->contract->locationDetection->neighborhood,
            'notes' => $this->notes,
            'status' => $this->status,
            'doorSize' => $this->doorSize->name,
            'doorsNumber' => $this->doors_number,
            'contract_number' => $this->contract->contract_number,
            'elevatorType' => $this->contract->elevatorType,
            'stopsNumber' => $this->contract->stopsNumbers,
            'externalDoorSpecification' => $this->externalDoorSpecification,
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

// 'id' => $this->id,
// 'contract_id' => $this->contract->id,
// 'contract' => $this->contract->location_data,
// 'client' => $this->contract->client,
// 'doorSpecification' => $this->doorSpecification,
// 'doorCover' => $this->doorCover,
// 'doors_number' => $this->doors_number,
// 'notes' => $this->notes,
// 'started_date' => $this->started_date,
// 'accept_date' => $this->accept_date,
// 'ended_date' => $this->ended_date,
// 'status' => $this->status,
// 'order_attached' => $this->order_attached
