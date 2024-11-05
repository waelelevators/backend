<?php

namespace Modules\Maintenance\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Maintenance\Http\Resources\MaintenanceVisitResource;

class MaintenanceContractDetailResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'installation_contract_id' => $this->installation_contract_id,
            'customer_id' => $this->customer_id,
            'user_id' => $this->user_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'visits_count' => $this->visits_count,
            'visit_start_date' => $this->visit_start_date,
            'cost' => $this->cost,
            'reming_cost' => $this->cost - $this->paid_amount,
            'paid_amount' => $this->paid_amount,
            'notes' => $this->notes,
            'remaining_visits' => $this->remaining_visits,
            'cancellation_allowance' => $this->cancellation_allowance,
            'payment_status' => $this->payment_status,
            'receipt_attachment' => $this->receipt_attachment,
            'contract_attachment' => $this->contract_attachment,
            'maintenance_type' => $this->maintenance_type,
            'status' => $this->status,

            'visits' => $this->when($this->relationLoaded('visits'), MaintenanceVisitResource::collection($this->visits)),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
