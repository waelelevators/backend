<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\MaintenanceQuotation;
use App\Models\Quotation;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Maintenance\Http\Requests\MaintenanceQuotationStoreRequest;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return MaintenanceQuotation::orderByDesc('created_at')->get();
    }

    public function search($idNumber)
    {
        // whereJsonContains('data->id_number',
        $maintenanceQuotations = MaintenanceQuotation::whereHas('client', function ($query) use ($idNumber) {
            $query->whereJsonContains('data->id_number', $idNumber);
        })->get();


        return $maintenanceQuotations;
    }

    /**
     * Store a newly created resource in storage.
     * @param MaintenanceQuotationStoreRequest $request
     * @return Renderable
     */
    public function store(MaintenanceQuotationStoreRequest $request)
    {
        $client =  ApiHelper::handleClientData($request); // العميل

        // Create a new MaintenanceQuotation instance
        $quotation = new MaintenanceQuotation();

        $quotation->client_id = $client->id;
        $quotation->amount = $request['amount'];
        $quotation->location_data = [
            'region'         => intval($request['region']),
            'city'           => intval($request['city']),
            'neighborhood'   => $request['neighborhood'],
            'street'         => $request['street'],
            'building_image' => "",
            'location_url'   => $request['locationBuilding'],
            'lat'            => $request['lat'] ?? '',
            'long'           => $request['long'] ?? ''
        ];

        $quotation->elevator_data = [
            'building_type_id'       =>  intval($request['buildingType']),
            'elevator_type_id'       =>  intval($request['elevatorType']),
            'stop_number_id'         =>  intval($request['stopsNumber']),
            'machine_speed_id'       =>  intval($request['machineSpeed']),
            'door_size_id'           =>  intval($request['doorSize']),
            'control_card_id'        =>  intval($request['controlCard']),
            'machine_type_id'        =>  intval($request['machineType']),

            'started_date'           =>  intval($request['startDate']),
            'ended_date'        =>  intval($request['endDate']),
            'visits_number'        =>  intval($request['totalVisit']),

            'is_there_window'        =>  intval($request['isHaveDoor'] ?? ''),
            'is_there_stair'         =>  intval($request['isLadder'] ?? ''),
        ];
        $quotation->how_did_you_get_to_us =  intval($request['reachUs']);
        $quotation->user_id = Auth::guard('sanctum')->user()->id;
        $quotation->save();

        ApiHelper::handleGetUsData($request, $quotation->id, 'main-quotations'); // كيف وصلت لنا

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة عرض السعر بنجاح',
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return MaintenanceQuotation::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
        $item =  MaintenanceQuotation::findOrFail($id);

        $item->delete();

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
}
