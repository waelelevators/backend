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
        //  $contract = $this->locationStatus->assignment->contract;

        return [
            'id' => $this->id,
            'contract_number' => $this->contract->contract_number,
            'contract_id' => $this->contract->id,
            'door_size' => $this->contract->doorSize->name,
            'machine_type' => $this->contract->machineType->name,
            'machine_weight' => $this->contract->machineLoad->name,
            'machine_speed' => $this->contract->machineSpeed->name,
            'contract_status' => $this->contract->contract_status,
            'client' => $this->contract->locationDetection->client->name,
            'phone' => $this->contract->locationDetection->client->phone,
            'elevatorType' => $this->contract->elevatorType->name,
            'manager_approval' => $this->manager_approval,
            'stopNumber' => $this->contract->stopsNumbers->name,
            'city' => $this->contract->locationDetection->city->name,
            'neighborhood' => $this->contract->locationDetection->neighborhood->name,
            'technicians' => $this->technicians,
            'statusName' => $this->status->name,
            'statusValue' => $this->status->value,
            'statusColor' => $this->status->color,
            'freeze' => $this->freeze,
            'duration' => $this->duration,
            'stageName' => $this->stage->name,
            'stageId' => $this->stage->id,
            'cabinStatus' => $this->contract->cabinStatus,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'created_at' => $this->created_at,
        ];
    }

    public function transformData()
    {
        return [
            'id' => $this->id,
            'contract_number' => $this->contract->contract_number,
            'client' => $this->contract->locationDetection->client->name,
            'phone' => $this->contract->locationDetection->client->phone,
            'technicians' => $this->technicians,
        ];
    }
}
