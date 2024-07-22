<?php

namespace Modules\Stock\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $contract = $this->locationStatus->assignment->contract;

        return [
            'id' => $this->id,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'contract_number' => $contract->contract_number,
            'client' => $contract->locationDetection->client,
            'elevatorType' => $contract->elevatorType,
            'stopsNumber' => $contract->stopsNumbers,
            'stageName' => $this->stage,
            'city' => $contract->locationDetection->city,
            'neighborhood' => $contract->locationDetection->neighborhood
        ];
    }
}
