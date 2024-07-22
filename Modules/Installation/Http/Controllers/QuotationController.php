<?php

namespace Modules\Installation\Http\Controllers;

use Alkoumi\LaravelArabicNumbers\Numbers;
use App\Helpers\ApiHelper;
use App\Helpers\PdfHelper;
use App\Models\InstallationQuotation;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Installation\Http\Requests\QuotationStoreRequest;
use Modules\Installation\Http\Resources\QuotationResource;
use Mpdf\Mpdf;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $models = InstallationQuotation::orderByDesc('created_at')->get();

        // return $models;
        return QuotationResource::collection($models);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(QuotationStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $client =  ApiHelper::handleClientOfferData($request); // العميل

            $representative_id =  ApiHelper::handleGetUsData($request, 'installations-quotation'); // كيف وصلت لنا

            // Create a new MaintenanceQuotation instance
            $quotation = new InstallationQuotation();

            $installments = is_array($request['installments']) ?
                $request['installments'] :
                array($request['installments']);

            //$installments = is_array($request['installments']) ? $request['installments'] : [$request['installments']];

            if (isset($request['buildingImage']))
                $building_image = ApiHelper::uploadBase64Image(
                    $request['buildingImage'],
                    'building'
                ); // صورة العمارة
            else $building_image = '';

            $quotation->project_name = $request['project_name'] ?? '';
            $quotation->client_id = $client->id;
            $quotation->total_price = $request['total_price'];
            $quotation->tax = $request['tax'];
            $quotation->discount = $request['discount'];
            $quotation->installments = json_encode($installments);
            $quotation->location_data = [
                'region'         => intval($request['region']) ?? '',
                'city'           => intval($request['city']) ?? '',
                'neighborhood'   => $request['neighborhood'] ?? '',
                'street'         => $request['street'] ?? '',
                'building_image' => $building_image ?? '',
                'location_url'   => $request['locationBuilding'] ?? '',
                'lat'            => $request['lat'] ?? '',
                'long'           => $request['long'] ?? ''
            ];

            $quotation->elevator_data = [
                'elevator_type_id'       =>  intval($request['elevatorType']) ?? '',
                'machine_type_id'        =>  intval($request['machineType']) ?? '',
                'machine_speed_id'       =>  intval($request['machineSpeed']) ?? '',
                'machine_load_id'        =>  intval($request['machineLoad']) ?? '',
                'machine_warranty_id'    =>  intval($request['machineWarranty']) ?? '',
                'people_load_id'         =>  intval($request['peopleLoad']) ?? '',
                'door_size_id'           =>  intval($request['doorSize']) ?? '',
                'stop_number_id'         =>  intval($request['stopsNumber']) ?? '',
                'control_card_id'        =>  intval($request['controlCard']) ?? '',
                'drive_type_id'          =>  intval($request['driveType']) ?? '',
                'entrances_number_id'    =>  intval($request['entrancesNumber']) ?? '',
                'elevator_trip_id'       =>  intval($request['elevatorTrip']) ?? '', // مشوار المصعد
                'elevator_room_id'       =>  intval($request['elevatorRooms']) ?? '' // غرفة الماكنية
            ];

            $quotation->representative_id = $representative_id;
            $quotation->notes =  $request['notes'];
            $quotation->more_adds =  collect($request['addition']);
            $quotation->template_id =  $request['templateName'];
            $quotation->user_id = Auth::guard('sanctum')->user()->id;
            $quotation->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم اضافة عرض السعر بنجاح',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء اضافة عرض السعر. حاول مرة اخرى.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $model = InstallationQuotation::findOrFail($id);

        return $model;
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
        $quotation = InstallationQuotation::findOrFail($id);

        $installments = is_array($request['installments']) ?
            $request['installments'] :
            array($request['installments']);

        $quotation->project_name = $request['project_name'] ?? '';
        $quotation->total_price = $request['total_price'];
        $quotation->tax = $request['tax'];
        $quotation->discount = $request['discount'];
        $quotation->installments = json_encode($installments);
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
            'elevator_type_id'       =>  intval($request['elevatorType']),
            'stop_number_id'         =>  intval($request['stopsNumber']),
            'machine_speed_id'       =>  intval($request['machineSpeed']),
            'door_size_id'           =>  intval($request['doorSize']),
            'control_card_id'        =>  intval($request['controlCard']),
            'machine_type_id'        =>  intval($request['machineType'])
        ];


        $quotation->how_did_you_get_to_us =  intval($request['reachUs']);

        $quotation->save();

        //ApiHelper::handleGetUsData($request, $quotation->id, 'inst-quotations'); // كيف وصلت لنا

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
