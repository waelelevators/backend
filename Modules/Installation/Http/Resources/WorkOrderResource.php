<?php

namespace Modules\Installation\Http\Resources;

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
        //   return parent::toArray($request);
        $contract = $this->locationStatus->assignment->contract;

        return [
            'id' => $this->id,
            'contract_number' => $contract->contract_number,
            'contract_status' => $contract->contract_status,
            'client' => $contract->locationDetection->client->name,
            'phone' => $contract->locationDetection->client->phone,
            'elevatorType' => $contract->elevatorType->name,
            'manager_approval' => $this->manager_approval,
            'stopNumber' => $contract->stopsNumbers->name,
            'city' => $contract->locationDetection->city->name,
            'neighborhood' => $contract->locationDetection->neighborhood->name,
            'technicians' => $this->technicians,
            'statusName' => $this->status->name,
            'statusColor' => $this->status->color,
            'freeze' => $this->freeze,
            'duration' => $this->duration,
            'stage' => $this->stage->name,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'created_at' => $this->created_at,
        ];
    }
}
