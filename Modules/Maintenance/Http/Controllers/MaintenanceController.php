<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\Region;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;

class MaintenanceController extends Controller
{


<<<<<<< HEAD
=======
    public function maintenance()
    {

        $data = [];

        $tables = [
            "elevator_types",
            "machine_types",
            "machine_speeds",
            "door_sizes",
            "stops_numbers",
            "control_cards",
            "drive_types",
            "maintenance_types",
            "branches"
        ];


        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get();
        }

        return response()->json(['elevator' => $data]);
    }
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function location()
    {
        $data = [];

        $tables = [
<<<<<<< HEAD
            "elevator_types",
=======
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
            "machine_types",
            "machine_speeds",
            "door_sizes",
            "control_cards",
            "stops_numbers",
<<<<<<< HEAD
            'users',
            'templates'
=======
            'users'
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
        ];

        $regionsWithCity =  Region::whereHas('cities.neighborhoods')->with('cities.neighborhoods')->get();

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get(['id', 'name']);
        }

        return response()->json(['elevator' => $data, 'regionsWithCities' => $regionsWithCity]);
    }

    public function malfunction()
    {
        //
        $data = [];

        $tables = [
<<<<<<< HEAD
=======
         
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
            'malfunction_types',
            'malfunction_statuses'
        ];

        $tables2 = [
            'products'
        ];

        foreach ($tables as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get(['id', 'name']);
        }
        foreach ($tables2 as $table) {
            // get name and id for each table
            $data[$table] = DB::table($table)->get(['id', 'sale_price', 'name']);
        }

        return response()->json(['elevator' => $data]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
