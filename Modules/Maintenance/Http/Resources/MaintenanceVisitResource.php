<?php

namespace Modules\Maintenance\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceVisitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'maintenance_contract_detail_id' => $this->maintenance_contract_detail_id,
            'technician' => $this->when($this->relationLoaded('user'), $this->user),
            'user' => $this->when($this->relationLoaded('user'), $this->user),
            'visit_date' => $this->visit_date,
            'status' => $this->status,
            'visit_start_date' => $this->visit_start_date,
            'visit_end_date' => $this->visit_end_date,
            'notes' => $this->notes,
            'test_checklist' => $this->test_checklist,
            'client_approval' => $this->client_approval,
            'maintenance_contract' => $this->when($this->relationLoaded('maintenanceContract'), $this->maintenanceContract),
            'images' => $this->images,
            'logs' => $this->when($this->logs, $this->logs),
        ];
    }
}
