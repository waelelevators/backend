<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\Area;
use App\Models\BuildingType;
use App\Models\City;
use App\Models\Client;
use App\Models\DoorSize;
use App\Models\ElevatorType;
use App\Models\MaintenanceContract;
use App\Models\Neighborhood;
use App\Models\StopNumber;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index($param, $year = 2024)
    {

        $analysisType = $param;
        $year = intval($year);


        // Determine the analysis type based on the $param value
        switch ($analysisType) {
            case 'area':
                $types = Area::all()->pluck('name');
                $countField = 'area_id';
                break;
            case 'elevator_type':
                $types = ElevatorType::pluck('name');
                $countField = 'elevator_type_id';
                $typeIds = ElevatorType::pluck('id');
                break;
            case 'has_stairs':
                $types = ['نعم', 'لا'];
                $countField = 'has_stairs';
                $typeIds = [0, 1];
                break;
            case 'stops_count':
                $types = StopNumber::pluck('name');
                $countField = 'stops_count';
                $typeIds = StopNumber::pluck('id');
                break;
            case 'door_size':
                $types = DoorSize::all()->pluck('name');
                $countField = 'door_size_id';
                $typeIds = DoorSize::pluck('id');
                break;
            case 'neighborhood':
                $types = Neighborhood::all()->pluck('name');
                $countField = 'neighborhood_id';
                break;
            case 'client':
                $types = Client::all()->pluck('name');
                $countField = 'client_id';
                break;

            case 'building_type':
                $types = BuildingType::all()->pluck('name');
                $countField = 'building_type_id';
                break;
            case 'has_window':
                $types = ['نعم', 'لا'];
                $countField = 'has_window';
                $typeIds = [0, 1];
                break;

            default:
                return response()->json(['error' => 'Invalid analysis type'], 400);
        }


        // return [
        //     'ids' => $typeIds,
        //     'countField' => $countField,
        //     'types' => $types,
        // ];
        // Initialize an array to store the analysis data
        $analysisData = [];

        // Iterate over the months of the specified year
        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
            $analysisData[$monthName] = [];

            // Iterate over the types
            for ($i = 0; $i < sizeof($typeIds); $i++) {
                // Count the number of maintenance contracts for the current type and month
                $count = MaintenanceContract::where($countField, $typeIds[$i])
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();
                if ($count == 0) {
                    continue;
                }

                $analysisData[$monthName][$types[$i]] = $count;
            }
        }

        return response()->json([
            'types' => $types,
            'analysis' => $analysisData,
        ]);
    }
}
