<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class OneColumnController extends Controller
{
    // Create
    public function store(Request $request, $tableName)
    {
        $request->validate(['name' => 'required|string']);

        try {
            DB::table($tableName)->insert($request->only('name'));
            Log::info("Inserted name into {$tableName}: {$request->name}");
            return response()->json(['message' => 'Data created successfully'], 201);
        } catch (\Exception $e) {
            Log::error("Error inserting data into {$tableName}: {$e->getMessage()}");
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Read
    public function index($table)
    {

        try {
            $names = DB::table($table)->get();
            Log::info("Fetched names from {$table}");
            return response()->json($names);
        } catch (\Exception $e) {
            Log::error("Error fetching names from {$table}: {$e->getMessage()}");
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update
    public function update(Request $request, $table, $id)
    {

        return $id;

        $request->validate(['name' => 'required|string']);

        try {
            DB::table($table)->where('id', $id)->update(['name' => $request->name]);
            Log::info("Updated name in {$table} with ID: {$id}");
            return response()->json(['message' => 'Name updated successfully']);
        } catch (\Exception $e) {
            Log::error("Error updating name in {$table} with ID {$id}: {$e->getMessage()}");
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Delete
    public function destroy($table, $id)
    {
        try {
            DB::table($table)->where('id', $id)->delete();
            Log::info("Deleted name from {$table} with ID: {$id}");
            return response()->json(['message' => 'Name deleted successfully']);
        } catch (\Exception $e) {
            Log::error("Error deleting name from {$table} with ID {$id}: {$e->getMessage()}");
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
