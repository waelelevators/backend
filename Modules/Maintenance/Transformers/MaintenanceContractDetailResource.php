<?php

namespace Modules\Maintenance\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Maintenance\Http\Resources\MaintenanceVisitResource;

class MaintenanceContractDetailResource extends JsonResource
{
    public function toArray($request)
    {

        // ahmed hmed yousif hmed

        return [
            'id' => $this->id,
            'project_name' => $this->project_name  ?? 'اسم المشروع',
            'installation_contract_id' => $this->installation_contract_id,
            'completed_visits_count' => $this->completed_visits_count,
            'maintenance_contract_id' => $this->maintenance_contract_id,
            'client' => $this->when($this->relationLoaded('client'), ($this->client)),
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
            'contract' => $this->when($this->relationLoaded('contract'), new MaintenanceContractResource($this->contract)),
            'last_report' => $this->when($this->relationLoaded('lastReport'), $this->lastReport),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}