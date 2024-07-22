<?php

namespace Modules\Installation\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Helpers\MyHelper;
use App\Models\Branch;
use App\Models\Contract;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\OuterDoorSpecification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Cache;
use Modules\Installation\Http\Requests\ContractStoreRequest;
use Modules\Installation\Http\Resources\ContractResource;
use Modules\Installation\Http\Resources\CoveringResource;
use Modules\Installation\Http\Resources\InstallmentsResource;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        $contracts = Contract::orderByDesc('created_at')->get();

        return  ContractResource::collection($contracts);
    }

    public function status(Request $request)
    {

        $contracts = Contract::where(['contract_status' => $request->status])->get();

        return  ContractResource::collection($contracts);
    }
    public function toCover()
    {

        $contracts = Contract::where('contract_status', 'assigned')
            ->get();

        $contracts = $contracts->filter(function ($contract) {
            return $contract->stage_id == 1 && $contract->externalStatus == 0 && $contract->door_number > 0 ||
                $contract->stage_id == 2 && $contract->cabinStatus == 0 ||
                $contract->stage_id == 3 && $contract->internalStatus == 0 && $contract->elevatorType->need_to_internal_door == 1;
        });



        // where(
        //     [
        //         ['doors_number', '>', 0],
        //         ['stage_id', '=', 1],
        //         ['contract_status', 'assigned']
        //     ]

        // )
        //     ->orWhereIn('stage_id', [2, 3])
        // $contracts = Contract::where(function ($query) {
        //     $query->where('doors_number', '>', 0)
        //         ->where('stage_id', 1);

        // return ($contract->ExternalStatus == 0 && $contract->stage_id == 1) ||
        // ($contract->CabinStatus == 0 && $contract->stage_id == 2) ||
        // ($contract->InternalStatus == 0 && $contract->stage_id == 3);

        // })
        //     ->where('contract_status', 'assigned')
        //     ->orWhere('stage_id', [2, 3])
        //     ->get()

        // $contracts = Contract::with('externalStatus', 'cabin', 'internal')
        //     ->get()
        //     ->filter(function ($contract) {
        //         return $contract->externalStatus == 0
        //             && $contract->cabin_status == 0
        //             && $contract->internal_status == 0;
        //     });

        // return $contracts;


        return  CoveringResource::collection($contracts);
    }

    public function representatives()
    {
        $contracts =  Contract::get();

        return $contracts->map(function ($contract) {

            return [
                'id' => $contract->id,
                'total' => $contract->total,
                'client' => $contract->locationDetection->client,
                'contract_number' => $contract->contract_number,
                'how_did_you_get_to_us' => $contract->representatives->how_did_you_get_to_us,
                'representatives' => $contract->representatives,
                'created_at' => $contract->created_at
            ];
        });
    }

    function installments(Request $request, $contract_id)
    {
        $installment =  Installment::with('contract')->where('contract_id', $contract_id)->get();

        return  InstallmentsResource::collection($installment);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(ContractStoreRequest $request, $id)
    {
        $contract = Contract::findOrFail($id);

        ApiHelper::updateUsData(
            $request,
            $request['representativeId']
        ); // كيف وصلت لنا

        $contract->project_name                                = $request['projectName'] ?? '';
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
        $contract->user_id                                     = Auth::guard('sanctum')->user()->id;
        // $contract->location_id                                 = $request['locationId'];
        // $contract->status                                      = 1;
        $contract->branch_id                                   = $request['branch']; // الفرع
        $contract->note                                        = $request['notes'];
        $contract->save();

        $dataArrss = is_array($request['paymentStages']) ?
            $request['paymentStages'] :
            array($request['paymentStages']);




        return response()->json([
            'status' => 'success',
            'message' => 'تم تعديل بيانات العقد بنجاح'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ContractStoreRequest $request)
    {
        DB::transaction(function () use ($request) {

            ApiHelper::updateClientData($request); // تحديت بيانات العميل

            $representative_id =  ApiHelper::handleGetUsData(
                $request,
                'installations'
            ); // كيف وصلت لنا

            $contract = new Contract();

            $data = [
                'region'          => $request['region'] ?? null,
                'city'            => $request['city'] ?? null,
                'neighborhood'    => $request['neighborhood'] ?? null,
                'street'          => $request['street'] ?? null,
                'location_url'    => $request['location_url'] ?? null,
                'lat'             => $request['lat'] ?? null,
                'long'            => $request['long'] ?? null
            ];

            // Remove null values from the data array
            $data = array_filter($data, function ($value) {
                return !is_null($value);
            });

            // InstallationLocationDetection::where(
            //     'id',
            //     $request['locationId']
            // )
            //     ->update([
            //         'location_data' => $data,
            //         'status' => 0
            //     ]); // تحديث بيانات كشف الموقع

            $contract->project_name                                = $request['projectName'] ?? '';
            $contract->total                                       = $request['priceIncludeTax'];
            $contract->tax                                         = $request['taxValue'];
            $contract->discount                                    = $request['discountValue'] ?? 0;
            $contract->elevator_type_id                            = $request['elevatorType'];
            $contract->doors_number                                = $request['doorsNumbers'];
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
            $contract->user_id                                     = Auth::guard('sanctum')->user()->id;
            $contract->location_id                                 = $request['locationId'];
            $contract->representative_id                           = $representative_id;
            $contract->status                                      = 1;
            $contract->template_id                                 = $request['template']; // قالب التصميم
            $contract->branch_id                                   = $request['branch']; // الفرع
            $contract->note                                        = $request['notes'];
            $contract->save();

            // ApiHelper::updateUsData($request, $request['representativeId']); // كيف وصلت لنا 

            $dataArr = is_array($request['externalDoorSpecifications']) ?
                $request['externalDoorSpecifications'] :
                array($request['externalDoorSpecifications']);

            foreach ($dataArr as $specifications) {

                $door                                = new OuterDoorSpecification();
                $door->contract_id                   = $contract->id;
                $door->floor                         = $specifications['floor'];
                $door->number_of_doors               = $specifications['door_number'];
                $door->out_door_specification        = $specifications['external_door_specifications'];
                $door->door_opening_direction        = $specifications['door_opening_direction'];
                $door->out_door_specification_tow    = $specifications['external_door_specifications2'] ?? '';
                $door->door_opening_direction_tow    = $specifications['door_opening_direction2'] ?? '';
                $door->save();
            }

            $dataArrss = is_array($request['paymentStages']) ?
                $request['paymentStages'] :
                array($request['paymentStages']);

            foreach ($dataArrss as $installObject) {

                $installment = new Installment;
                $installment->contract_id = $contract->id;
                $installment->paid_id     = $installObject['paid_id'];
                $installment->amount      = $installObject['amount'];
                $installment->tax         = $installObject['amountWithTaxed'];
                $installment->save();
            }

            $emails = User::where('level', 'installations')->get()->pluck('email');

            if ($emails->count() > 0) {

                MyHelper::pushNotification($emails, [
                    'title' => 'عقد جديد',
                    'body' => 'تم اضافة عقد جديد '
                ]);
            }
        });
        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة العقد بنجاح'
        ]);
    }

    public function payment(Request $request)
    {

        $data = [
            'amount' => $request->amount,
            'contract_id' => $request->contract_id,
            'files' => $request->files,
            'stage_id' => $request->stage,
        ];

        $validator = Validator::make($data, [
            'contract_id' => 'required',
            'stage_id' => 'required|integer|between:1,3',
            'amount' => [
                'required', 'numeric', 'gt:0',
                function ($attribute, $value, $fail) use ($request) {
                    $contract = Contract::find($request->contract_id);
                    $remainingAmount =  $contract->getRemainingAmountInStage($request->stage);
                    $isPreviousStagePaid =  $contract->isPreviousStagePaid($request->stage);

                    if ($value === null) {
                        $fail('المبلغ اجباري');
                    } elseif ($remainingAmount == 0) {
                        $fail('لقد تم دفع قسط المرحلة كاملأ');
                    } elseif (!$isPreviousStagePaid) {
                        $fail('الرجاء قم بدفع قسط المرحلة السابقة اولا');
                    } elseif ($value > $remainingAmount) {
                        $fail('المبلغ المراد دفعه لايمكن ان يكون اكبر من متبقي الدفعة ' . $remainingAmount);
                    }
                }
            ],

        ], [

            'amount.required' => 'المبلغ اجبارى',
            'amount.gt' => 'يجب ان يكون المبلغ  اكبر من صفر',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response([
                'errors' => $errors
            ], 422);
        }


        DB::transaction(function () use ($request, $data) {

            $contract = Contract::find($request->contract_id);

            $filePath = $this->uploadBase64Pdf(
                $request['files'],
                'contract/payments'
            );

            $payment = new Payment;
            $payment->contract_id = $request->contract_id;
            $payment->stage_id = $request->stage;
            $payment->amount = $request->amount;
            $payment->attachments = $filePath ?? null;
            $payment->save();


            ApiHelper::LocationAssignment($contract, $contract->id);

            if ($contract->getIsReadyToStart($request->stage)) {

                // Queue notifications for performance improvement
                $emails = Cache::remember('installation_and_purchase_emails', 60, function () {
                    return User::whereIn('level', ['installations', 'purchases'])->pluck('email');
                });

                MyHelper::pushNotification($emails, [
                    'title' => 'تم دفع مرحله للعقد رقم #' . $contract->id,
                    'body' => 'تم دفع المرحله ' . $contract->stage_id
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة الدفعية بنجاح'
        ]);
    }


    private function uploadBase64Pdf($base64Pdf, $path)
    {
        $pdfData = base64_decode(preg_replace('#^data:application/pdf;base64,#i', '', $base64Pdf));

        // Generate a unique filename
        $filename = uniqid() . '.pdf'; // You can adjust the extension based on the image format

        // Save the image to the storage directory
        Storage::disk('public')->put($path . '/' . $filename, $pdfData);

        $fullPath = 'storage/' . $path . '/' . $filename;

        return $fullPath;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {

        $model = Contract::findOrFail($id);

        return $model;
    }
    public function attachment(Request $request, $contract_id)
    {
        $contractModel = Contract::findOrFail($contract_id);

        $branch = Branch::find($contractModel->branch_id);

        // $request->validate([
        //     'attachment' => 'required|mimes:pdf', // Adjust the allowed file types and size as needed
        // ]);

        $filePath = $this->uploadBase64Pdf(
            $request['attachment'],
            'contract/signed'
        );

        $last = $branch->last_id;
        $branch->last_id = $last + 1;
        $code = $branch->prefix;
        $branch->save();

        $contract_number = $code . '-' . $last; // رقم العقد
        $contractModel->attachment = $filePath;
        $contractModel->contract_status = 'assigned';
        $contractModel->contract_number = $contract_number;
        $contractModel->save();

        ApiHelper::LocationAssignment($contractModel, $contract_id);

        // return $contractModel;
        // $contractModel = Contract::where('id', $contract_id)->update([
        //     'attachment' => $filePath,
        //     'contract_number' => $contract_number,
        //     'contract_status' => 'assigned',
        // ]);

        if ($contractModel)
            return response()->json([
                'message' =>
                'File uploaded successfully',
                'file_path' => $filePath
            ]);

        return response()->json([
            'message' =>
            'There is an error.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Find the contract
                $contract = Contract::findOrFail($id);

                // Delete related outer door specifications
                OuterDoorSpecification::where('contract_id', $contract->id)->delete();

                // Delete related installments
                Installment::where('contract_id', $contract->id)->delete();

                // Delete the contract itself
                $contract->delete();

                // Notify users about the deletion if necessary
                $emails = User::where('level', 'installations')->get()->pluck('email');
                if ($emails->count() > 0) {
                    MyHelper::pushNotification($emails, [
                        'title' => 'عقد محذوف',
                        'body' => 'تم حذف عقد'
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف العقد بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في حذف العقد!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
