<?php

namespace Modules\Maintenance\Services;

use App\Models\MaintenanceContractDetail;
use App\Models\MaintenanceVisit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MaintenanceVisitService
{
    public function createVisit(array $data)
    {
        $maintenanceContractDetail = MaintenanceContractDetail::findOrFail($data['maintenance_contract_detail_id']);

        $visitsCount = $maintenanceContractDetail->visits_count;
        $startDate = Carbon::parse($maintenanceContractDetail->start_date);

        $visits = [];

        for ($i = 0; $i < $visitsCount; $i++) {
            $visit = new MaintenanceVisit([
                'maintenance_contract_detail_id' => $maintenanceContractDetail->id,
                'technician_id' => $data['technician_id'],
                'visit_date' => $startDate->copy(),
                'status' => 'created',
                'user_id' => auth('sanctum')->user()->id,
                'maintenance_contract_id' => $maintenanceContractDetail->maintenance_contract_id,
            ]);

            $visit->save();
            $visits[] = $visit;

            $startDate->addDays(30);
        }

        return $visits;
    }
}