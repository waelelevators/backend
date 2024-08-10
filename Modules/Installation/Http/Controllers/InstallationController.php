<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\Client;
use App\Models\Region;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class InstallationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function contract()
    {

        $data = [];
        $clients = Client::all();
        $regionsWithCity =  Region::whereHas('cities.neighborhoods')->with('cities.neighborhoods')->get();
        $tables = [
            "floors", "elevator_types", 'external_door_specifications', "stops_numbers",
            "elevator_trips", "machine_loads", "people_loads", 'additions',
            "control_cards", "entrances_numbers", "outer_door_directions", "inner_door_types",
            "templates", "door_sizes", "elevator_rooms", "machine_types", "employees",
            "machine_speeds", "elevator_warranties", "counterweight_rails_sizes", "stages", "branches",
        ];



        // $entityList = $clients->map(function ($entity) {
        //     if ($entity['type'] == '1')
        //         return [
        //             'id' => $entity->id,
        //             'name' => $entity['data']['first_name'] . ' ' . $entity['data']['last_name'],
        //         ];


        //     if (in_array($entity['type'], ['2', '3']))
        //         return [
        //             'id' => $entity->id,
        //             'name' => $entity['data']['name'] ?? '',
        //         ];


        //     return null; // If the type is not 1, 2, or 3, return null to filter out later.

        // })->filter()->values(); // Filter out null values and re-index the array.

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get(['id', 'name']);
        }

        return response()->json(['elevator' => $data, 'regionsWithCities' => $regionsWithCity]);
    }

    public function quotation()
    {
        $data = [];

        $tables = [
            "elevator_types",
            "machine_types",
            "stops_numbers",
            "people_loads",
            "drive_types",
            "machine_loads",
            "elevator_warranties",
            "machine_speeds",
            "control_cards",
            "door_sizes",
            "entrances_numbers",
            "additions",
            "templates",
            "elevator_trips",
            "elevator_rooms"
        ];

        $regionsWithCity =  Region::whereHas('cities.neighborhoods')->with('cities.neighborhoods')->get();

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get(['id', 'name']);
        }

        return response()->json(['elevator' => $data, 'regionsWithCities' => $regionsWithCity]);
    }
    public function location()
    {
        $data = [];

        $tables = [
            "elevator_types", "stops_numbers", "elevator_trips", "weight_locations",
            "users", "floors", "inner_door_types", "entrances_numbers"
        ];

        $regionsWithCity =  Region::whereHas('cities.neighborhoods')->with('cities.neighborhoods')->get();

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get(['id', 'name']);
        }

        return response()->json(['elevator' => $data, 'regionsWithCities' => $regionsWithCity]);
    }

    public function products()
    {
        $data = [];

        $tables = [
            "stages", "elevator_types", "stops_numbers", "products"
        ];

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get(['id', 'name']);
        }

        return response()->json(['data' => $data]);
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function doorManufacture()
    {
        $data = [];

        $tables = [
            "colors", "outer_door_directions"
        ];

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get();
        }

        return response()->json(['elevator' => $data]);
    }

    public function cabinManufacture()
    {
        $data = [];

        $tables = [
            "cover_types", "weight_locations", "door_sizes", "inner_door_types", "external_door_specifications"
        ];

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get();
        }

        return response()->json(['elevator' => $data]);
    }
}
