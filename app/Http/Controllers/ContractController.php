<?php

namespace App\Http\Controllers;


use App\Http\Requests\ContractStoreRequest;
use App\Http\Resources\ContractResource;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Contract;
use App\Models\ContractProductQuantity;
use App\Models\Installment;
use App\Models\LocationAssignment;
use App\Models\OuterDoorSpecification;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationD;
use App\Models\Representative;
use App\Models\Stage;
use App\Models\User;
use App\Service\ContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    /**
     * @param \App\Http\Requests\ContractUpdateRequest $request
     * @param \App\Models\Contract $contract
     * @return \App\Http\Resources\ContractResource
     */
    public function update(Request $request, $id)
    {
        $contract =  Contract::findOrFail($id);

        if (isset($request['building_image'])) $building_image = $this->uploadBase64Image(
            $request['building_image'],
            'maintenances'
        );

        else $building_image = '';

        $contract->project_name  = $request['projectName'] ?? 'لايوجد';
        $contract->location_data = [
            'region'         => $request['region'],
            'city'           => $request['city'],
            'neighborhood'   => $request['neighborhood'],
            'street'         => $request['street'],
            'building_image' => $building_image,
            'location_url'   => $request['location_url'],
            'lat'            => $request['lat'],
            'long'           => $request['long']
        ];

        $contract->total                                       = $request['priceIncludeTax'];
        $contract->tax                                         = $request['taxValue'];
        $contract->discount                                    = $request['discountValue'] ?? 0;
        $contract->elevator_type_id                            = $request['elevatorType'];
        $contract->cabin_rails_size_id                         = $request['cabinRailsSize'];
        $contract->stop_number_id                              = $request['stopsNumber'];
        $contract->elevator_trip_id                            = $request['elevatorTrip'];
        $contract->elevator_warranty_id                        = $request['elevatorWarranty'];
        $contract->entrances_number_id                         = $request['entrancesNumber'];
        $contract->free_maintenance_id                         = $request['freeMaintenance'];
        $contract->inner_door_type_id                          = $request['innerDoorType'];
        $contract->machine_load_id                             = $request['machineLoad'];
        $contract->machine_speed_id                            = $request['machineSpeed'];
        $contract->outer_door_direction_id                     = $request['outerDoorDirection'];
        $contract->people_load_id                              = $request['peopleLoad'];
        $contract->visits_number                               = $request['totalFreeVisit'];
        $contract->door_size_id                                = $request['doorSize'];
        $contract->control_card_id                             = $request['controlCard'];
        $contract->stage_id                                    = $request['stage'];
        $contract->elevator_room_id                            = $request['elevatorRoom'];
        $contract->machine_warranty_id                         = $request['machineWarranty'];
        $contract->other_additions                             = collect($request['otherAdditions']);
        $contract->machine_type_id                             = $request['machineType'];
        $contract->counterweight_rails_size_id                 = $request['counterweightRailsSize'];
        $contract->note                                        = $request['notes'];
        $contract->how_did_you_get_to_us                       = $request['reachUs'];
        $contract->save();

        return response()->json([
            'error' => false,
            'message' => 'تم تعديل البيانات بنجاح'
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Contract $contract)
    {
        $contract->delete();

        return response()->noContent();
    }

    function products($contract_id, $stage_id)
    {
        // طلبات البضاعة 
        //عرض البضاعة المراد طلبها حسب المرحلة والعقد 

        $contract = Contract::where('id', $contract_id)->first();
        // $stage = $contract->stage_id;
        $stage = $stage_id;


        $FilterContract = [
            'contract_number' => $contract?->contract_number,
            'name' => $contract?->locationDetection?->client->name,
            'project_name' => $contract?->project_name,
            'door_size' => $contract?->doorSize->name,
            'elevator_type' => $contract?->elevatorType->name,
            'outer_door_directions' => $contract?->outerDoorDirections?->name,
            'inner_door_type' => $contract?->innerDoorType->name,
            'machine_speed' => $contract?->machineSpeed->name,
            'machine_type' => $contract?->machineType->name,
            'stops_numbers' => $contract?->stopsNumbers->name,
            'elevator_trip' => $contract?->elevatorTrip->name,
            'stage' => $contract?->stage->name,
            'total' => $contract?->total,
        ];

        $productsQyt = ContractProductQuantity::where('stage_id', $stage)
            ->where('elevator_type_id', $contract->elevator_type_id)
            ->where('floor_id', $contract->stop_number_id)
            ->with('product')
            ->get();

        $quotation =  Quotation::where('contract_id', $contract_id)
            ->where('stage', $stage)
            ->with(['quotation_d'])
            ->get();


        if ($quotation->count() > 0) { // تم طلب بضاعة المرحلة المعينة مسبقا
            return [
                'quotation' => $quotation,
                'contract' => $contract,
                'products_qyt' => $productsQyt
            ];
        }

        $products =  Product::where('stage', $stage)->get(); // في حالة الرغبة في اضافة منتج اضافي
        return [
            'contract' => $FilterContract,
            'products' => $products,
            'products_qyt' => $productsQyt
        ];
    }

    function createQuotation(Request $request, $contract_id)
    {
        $contract = Contract::find($contract_id);
        // $stage = $contract->stage_id;
        $stage = $request->stage_id;
        $products =  $request->data;

        // check if stage has quotation already
        if ($stage == 1 && $contract->stage_one()->count() > 0) {
            // bad request code
            return response()->json([
                'error' => true,
                'message' => 'هذة المرحله لديها عرض سعر مسبقا'
            ], 400);
        } elseif ($stage == 2 && $contract->stage_two()->count() > 0) {
            // bad request code
            return response()->json([
                'error' => true,
                'message' => 'هذة المرحله لديها عرض سعر مسبقا'
            ], 400);
        } elseif ($stage == 3 && $contract->stage_three()->count() > 0) {
            // bad request code
            return response()->json([
                'error' => true,
                'message' => 'هذة المرحله لديها عرض سعر مسبقا'
            ], 400);
        }

        // $payed = $contract->payments->sum('amount');
        // $total = $contract->total;
        // $required_percentage = Stage::find($stage)->required_percentage;

        // if ($payed >= ($total * $required_percentage / 100)) {
        // } else {
        //     return response([
        //         'message' => 'يجب عليك دفع المرحله لتتمكن من انشاء عرض سعر',
        //     ], 400);
        // }

        $quotation = new Quotation();
        $quotation->contract_id = $contract_id;
        $quotation->stage = $stage;
        $quotation->save();

        foreach ($products as $product) {
            $quotation_product = new QuotationD();
            $quotation_product->quotation_id = $quotation->id;
            $quotation_product->product_id = $product['product_id'];
            $quotation_product->quantity = $product['qty'];
            $quotation_product->save();
        }
        return response()->json([
            'error' => false,
            'message' => 'تم اضافة العرض بنجاح',
            'data' => $quotation
        ]);
    }



    private function uploadBase64Image($base64Image, $path = 'logos')
    {
        // Decode the base64-encoded image
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Generate a unique filename
        $filename = uniqid() . '.png'; // You can adjust the extension based on the image format

        // Save the image to the storage directory
        Storage::disk('public')->put($path . '/' . $filename, $imageData);

        $fullPath = asset('storage/' . $path . '/' . $filename);

        return $fullPath;
    }
}
