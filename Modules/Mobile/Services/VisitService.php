<?php

namespace Modules\Mobile\Services;

use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractDetail;
use App\Models\MaintenanceVisit;
use Carbon\Carbon;
use Modules\Mobile\Http\Resources\VisitsResource;

class VisitService
{
    public function getAllVisits()
    {

        $contracts = MaintenanceContract::paginate(10);
        // return $contracts;
        return MaintenanceVisit::with([
            'maintenanceContract.client',
            'maintenanceContract.city',
            'maintenanceContract.neighborhood',
            'maintenanceContract.area',
            'maintenanceContractDetail',
            'user',
        ])
            // where has latitude
            // ->whereNotNull('latitude')
            ->take(10)->get();
        // ->map(function ($contract) {
        //     return [
        //         'id' => $contract->id,
        //         'contract_number' => $contract->contract_number,
        //         'latitude' => $contract->latitude,
        //         'longitude' => $contract->longitude
        //     ];
        // })
        // ->toArray();
    }

    public function getVisitById($id)
    {
        $visit = MaintenanceVisit::with([
            'maintenanceContract.client',
            'maintenanceContract.city',
            'maintenanceContract.neighborhood',
            'maintenanceContract.area',
            'maintenanceContractDetail',
            'user',
        ])->findOrFail($id);

        return new VisitsResource($visit);
    }

    public function updateLocation($id, array $data)
    {
        $visit = MaintenanceVisit::with('maintenanceContract')->findOrFail($id);

        if ($visit->maintenanceContract) {
            $visit->maintenanceContract->update([
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);
        }

        return $visit->fresh('maintenanceContract');
    }

    public function updateVisit($id, array $data)
    {

        $visit = MaintenanceVisit::findOrFail($id);
        $this->checkContractStatus($visit->maintenance_contract_detail_id);

        if (isset($data['updateStatus']) && $data['updateStatus'] == true) {
            if ($data['status'] == 'ongoing') {
                $visit->update(['visit_start_date' => now(), 'status' => 'ongoing']);
            }
            if ($data['status'] == 'completed') {
                $visit->update(['visit_end_date' => now(), 'status' => 'completed']);
            }
        } else {
            $visit->test_checklist = $data['maintenanceItems'];
            $visit->notes = $data['notes'];
        }
        $visit->save();
        return $visit;
    }

    public function getVisitsSortedByDistance($latitude, $longitude)
    {
        $visits = $this->getAllVisits();

        $sortedVisits = $visits->map(function ($visit) use ($latitude, $longitude) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $visit->maintenanceContract->latitude ?? 0,
                $visit->maintenanceContract->longitude ?? 0
            );
            $visit->distance = $distance;
            return $visit;
        })->sortBy('distance');

        return VisitsResource::collection($sortedVisits);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // بالكيلومترات

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }


    private function checkContractStatus($contract_id)
    {
        $contract = MaintenanceContractDetail::find($contract_id);

        // Get current date
        $now = Carbon::now();

        // Check if contract has ended
        $contractEnded = $contract->end_date && Carbon::parse($contract->end_date)->lt($now);

        // Count remaining visits
        $remainingVisits = $contract->remaining_visits;

        // If contract has ended or no remaining visits, mark as expired
        if ($contractEnded || $remainingVisits <= 0) {
            $contract->update(['status' => 'expired', 'remaining_visits' => 0]);
        }
    }
}
