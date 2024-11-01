<?php

namespace Modules\Maintenance\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MaintenanceUpgradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        //  json_decode($value, true);
        return [
            'id' => $this->id,
            'total' => $this->total,
            'tax' => $this->tax,
            'notes' => $this->notes,
            'rejection_reason' => $this->rejection_reason,
            'stops_count' => $this->stops_count,
            'has_stairs' => $this->has_stairs,
            'status' => $this->status->toArray(),
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'speed' => $this->speed,
            'attachment_contract' => $this->attachment_contract ? asset('storage/' . $this->attachment_contract) : null,
            'attachment_receipt' => $this->attachment_receipt ? asset('storage/' . $this->attachment_receipt) : null,
            'elevator_type' => $this->elevatorType,
            'building_type' => $this->buildingType,
            'user' => $this->user,
            'client' => $this->client,
            'products' => $this->products,
            'logs' => $this->logs,
        ];
    }
}