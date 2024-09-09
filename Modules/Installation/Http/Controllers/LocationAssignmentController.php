<?php

namespace Modules\Installation\Http\Controllers;


use App\Models\LocationAssignmentsLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\LocationAssignment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Modules\Installation\Http\Requests\LocationAssignmentUpdateRequest;
use Modules\Installation\Http\Resources\LocationAssignmentResource;

class LocationAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        // $models = LocationAssignment::select('location_assignments.*')
        //     ->join(
        //         DB::raw('(SELECT MIN(id) as id FROM location_assignments WHERE status != 3 GROUP BY contract_id) as earliest'),
        //         'earliest.id',
        //         '=',
        //         'location_assignments.id'
        //     )
        //     ->get();

        // $models = Contract::with('assig')->get();
        // $models = Contract::whereHas('assignments', function ($query) {
        //      $query->where('stage_id', $query->stage_id);
        // })->get();
        // $models = Contract::whereHas('assignments', function ($query) {
        //     $query->where('stage_id', function ($query) {
        //         $query->select('stage_id')
        //             ->from('contracts')
        //      ->whereColumn('contract_id', 'contracts.id');
        //     });
        // })->with('assig')->get();

        $subquery = LocationAssignment::select(
            'contract_id',
            DB::raw('MAX(stage_id) as max_stage_id')
        )
            ->where('status', '!=', 3)
            ->groupBy('contract_id');

        $results = LocationAssignment::joinSub($subquery, 'sub', function ($join) {
            $join->on('location_assignments.contract_id', '=', 'sub.contract_id')
                ->on('location_assignments.stage_id', '=', 'sub.max_stage_id');
        })
            ->select('location_assignments.*')
            ->orderBy('created_at', 'ASC')
            ->get();

        //return $results;

        return  LocationAssignmentResource::collection($results);
    }

    public function representative($id)
    {

        // status[
        //     1=>'غير مسند'
        //     2=>'مسند'
        // ]
        // $contracts = Contract::whereHas('representatives', function ($query) use ($id) {
        //     $query->where('representativeable_id', $id);
        // })->get();

        $contracts = LocationAssignment::where('representative_by', $id)
            ->where('location_assignments.status', '!=', '3')
            ->where(function ($query) {
                $query->orWhere('financial_status', 3)->orWhere('status', 2);
            })
            ->get();

        return  LocationAssignmentResource::collection($contracts);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $locationAssignment  = LocationAssignment::with(['logs'])->findOrFail($id);
        return $locationAssignment;
        //  return new LocationAssignmentResource($locationAssignment->toShow());
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(LocationAssignmentUpdateRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            LocationAssignment::where('id', $id)
                ->update([
                    'status' => $request['status'],
                    'representative_by' => $request['representative_by']
                ]);


            $LocationLogo = new LocationAssignmentsLog();

            $LocationLogo->location_assignment_id = $id;
            $LocationLogo->representative_by = $request['representative_by'];
            $LocationLogo->status = $request['status'];
            $LocationLogo->user_id = Auth::guard('sanctum')->user()->id;
            $LocationLogo->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم التعديل بنجاح',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
