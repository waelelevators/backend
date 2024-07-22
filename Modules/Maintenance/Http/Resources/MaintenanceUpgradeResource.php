<?php

namespace Modules\Maintenance\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'id'=>$this->id,
            'total_cost' => $this->total_cost,
            'tax' => $this->tax,
            'notes' => $this->notes,
            'elevators_parts' => $this->elevators_parts,
            'status'=> $this->status,
        ];
    }
}
