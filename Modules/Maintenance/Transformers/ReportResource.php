<?php

namespace Modules\Maintenance\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'maintenance_contract_id' => $this->maintenance_contract_id,
            'status' => $this->status,
            'problems' => $this->problems,
            'tax' => $this->tax,
            'price_without_tax' => $this->price_without_tax,
            'discount' => $this->discount,
            'final_price' => $this->final_price,
            'technician_id' => $this->technician_id,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'faults' => $this->faults ?? [],
            'logs' => $this->logs ?? [],
            'products' => $this->requiredProducts ?? [],
            'user' => $this->user ?? [],
            'technician' => $this->technician ?? [],
        ];
    }
}
