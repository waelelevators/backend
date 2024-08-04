<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\City;
use App\Models\Contract;
use App\Models\InstallationLocationDetection;
use App\Models\InstallationQuotation;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{

    function regions()
    {
        $Cities = City::get(['name', 'id']);
        $currentYear = date('Y');
        $currentMonth = date('n'); // Get current month as a number

        // Array to map month number to month name
        $months = [

            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'ابريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'

        ];
        // Query to get contracts data
        $contracts = Contract::selectRaw("
                    MONTH(contracts.created_at) as month,
        installation_location_detections.city as city_name,
        COUNT(*) as total_contracts")
            ->join(
                'installation_location_detections','contracts.elevator_type_id',
                '=',
                'installation_location_detections.id'
            )
            ->whereYear('contracts.created_at', $currentYear)
            ->whereMonth('contracts.created_at', '<=', $currentMonth)
            ->where('contract_status', '!=', 'Draft')
            ->groupBy('city_name', 'month')
            ->orderBy('month')
            ->get();

        // Map the query results to the desired structure
        foreach ($contracts as $contract) {
            $monthName = $months[$contract->month];
            $elevatorName = $contract->elevator_name;

            // Initialize the month if it doesn't exist
            if (!isset($result[$monthName])) {
                $result[$monthName] = [
                    'month' => $monthName
                ];
            }

            // Assign the total contracts to the corresponding machine name
            if ($contract->total_contracts > 0) {
                $result[$monthName][$elevatorName] = $contract->total_contracts;
            }
        }
        $finalResult = array_values($result);

        return response()->json(
            [
                'kpi' => $finalResult,
                'cities' => $Cities
            ]
        );
    }
    function countContracts()
    {
        $Quotations = InstallationQuotation::count();
        $installationLocationDetections = InstallationLocationDetection::count();
        $SignedContracts = Contract::where('contract_status', 'assigned')->count();
        $NotSignedContracts = Contract::where('contract_status', 'Draft')->count();


        return response()->json([
            'installationLocationDetections' => $installationLocationDetections,
            'Quotations' => $Quotations,
            'SignedContracts' => $SignedContracts,
            'NotSignedContracts' => $NotSignedContracts
        ]);
    }
}
