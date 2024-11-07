<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Models\Client;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractDetail as ModelsMaintenanceContractDetail;
use App\Service\Base64FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Maintenance\Entities\MaintenanceContract as EntitiesMaintenanceContract;
use Modules\Maintenance\Entities\MaintenanceContractDetail;
use Modules\Maintenance\Http\Requests\MaintenanceContractStoreRequest;
use Modules\Maintenance\Services\MaintenanceContractService;
use Modules\Maintenance\Transformers\MaintenanceContractResource;



class MaintenanceContractController extends Controller
{
    protected $maintenanceContractService;

    public function __construct(MaintenanceContractService $maintenanceContractService)
    {
        $this->maintenanceContractService = $maintenanceContractService;
    }



    public function index(string $type = null)
    {

        $area_id = request()->query('area_id');

        $contractsQuery = EntitiesMaintenanceContract::with('area', 'city', 'neighborhood', 'elevatorType')
            ->where('contract_type', $type ?? 'contract')
            ->when($area_id, fn($query) => $query->where('area_id', $area_id));

        return MaintenanceContractResource::collection($contractsQuery->latest()->paginate(10));
    }



    public function store(MaintenanceContractStoreRequest $request)
    {

        if ($request->has('contract_id') && $request->contract_id > 0) {
            $contract = $this->maintenanceContractService->convertDraftToContract($request->all());
        } else {

            $contract = $this->maintenanceContractService->createContract($request->all());
        }
        return response([
            'message' => 'Contract created successfully',
            'status' => 'success',
        ], 201);
        return new MaintenanceContractResource($contract);
    }


    // call api and sow how can you do that
    function update(Request $request)
    {
        $request->validate([
            'isDraft' => 'required|boolean',
            'contract_id' => 'required|exists:maintenance_contracts,id',
        ]);
        if ($request->isDraft  == true) {
            $this->maintenanceContractService->updateDraftContract($request->all());
        } else {
            $this->maintenanceContractService->updateContract($request->all());
        }
        return response([
            'message' => 'Contract updated successfully',
            'status' => 'success',
        ]);
    }

    public function show($id)
    {
        $contract = EntitiesMaintenanceContract::findOrFail($id);
        return new MaintenanceContractResource($contract);
    }


    public function searchClients(Request $request)
    {

        $request->validate([
            'elevator_type_id' => 'required|exists:elevator_types,id',
            'phone' => 'required|exists:clients,phone',
        ], [
            'phone.exists' => 'العميل غير موجود',
            'elevator_type_id.exists' => 'نوع المصعد غير موجود',
        ]);
        // $client_id = Client::where('phone', $request->phone)->findOrFail()->id;

        $client_id = Client::where('phone', $request->phone)->first()->id;

        $maintenance_contracts = EntitiesMaintenanceContract::where('elevator_type_id', $request->elevator_type_id)
            ->where('client_id', $client_id)
            ->where('contract_type', 'draft')
            ->with('area', 'city', 'neighborhood', 'elevatorType')
            ->get();



        $clients =  MaintenanceContractResource::collection($maintenance_contracts);

        $data = [
            'status' => 'success',
            'data' => MaintenanceContractResource::collection($maintenance_contracts)
        ];
        return response($data);
    }


    public function convertDraftToContract(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'start_date' => 'required',
            'visits_count' => 'required',
            'end_date' => 'required',
        ]);

        $contract = $this->maintenanceContractService->convertDraftToContract($request->all());
        return new MaintenanceContractResource($contract);
    }



    public function endContract(Request $request)
    {
        $this->maintenanceContractService->endContract($request->all());
        return response([
            'message' => 'Contract ended successfully',
            'status' => 'success',
        ]);
    }


    // getExpiredContracts

    public function getExpiredContracts()
    {
        $ex = new MaintenanceContractDetail();
        $maintenanceContractsIds =  $ex->getExpiredContracts()->whereNotNull('maintenance_contract_id')->pluck('maintenance_contract_id');

        $contracts = MaintenanceContract::whereIn('id', $maintenanceContractsIds)->get();


        return MaintenanceContractResource::collection($contracts);
    }


    function renewContract(Request $request, $id)
    {

        $this->maintenanceContractService->renewContract($request->all(), $id);
        return response([
            'message' => 'Contract renewed successfully',
            'status' => 'success',
        ]);
    }




    // getUnpaidContracts

    public function getUnpaidContracts()
    {
        $maintenanceContractsIds = ModelsMaintenanceContractDetail::where('paid_amount', '<', 'cost')
            ->where('status', 'active')
            ->whereNotNull('maintenance_contract_id')->pluck('maintenance_contract_id');


        $contracts = MaintenanceContract::whereIn('id', $maintenanceContractsIds)->get();


        return MaintenanceContractResource::collection($contracts);
    }



    public function uploadFiles(Request $request)
    {

        $request->validate([
            'maintenance_contract_id' => 'required|exists:maintenance_contract_details,id',
            'attachment' => 'required|file',
            'cost' => 'required_if:attachment_type,receipt_attachment',
            'attachment_type' => 'required',
        ]);


        $contractDetails = MaintenanceContractDetail::findOrFail($request->maintenance_contract_id);

        $reminingAmount =  $contractDetails->cost - $contractDetails->paid_amount;






        if ($request->hasFile('attachment')) {
            $filePath = $this->storeFile($contractDetails->id, $request->file('attachment'));

            if ($request->attachment_type == 'contract_attachment') {
                $contractDetails->contract_attachment = $filePath;
                $contractDetails->save();
            } else {
                // if $request->const > $reminingAmount return error
                if ($request->cost > $reminingAmount) {
                    return response()->json([
                        'success' => 'error',
                        'message' => 'المبلغ المدفوع اكبر من المبلغ المتبقي للعقد'
                    ], 400);
                }
                $contractDetails->receipt_attachment  = $filePath;
                $contractDetails->save();


                // if remaining amount not zero add $request->const to paid_amount and payment sataus partially paid
                if ($reminingAmount != 0) {

                    $contractDetails->paid_amount += $request->const;
                    $contractDetails->payment_status = 'partial';
                    $contractDetails->save();
                } else {
                    $contractDetails->paid_amount += $request->const;
                    $contractDetails->payment_status = 'paid';
                    $contractDetails->save();
                }
            }

            return response([
                'status' => 'success',
                'message' => 'تم رفع المرفق بنجاح',
            ]);
        }
    }


    private function storeFile($contractId, $file)
    {
        $filename = $file->getClientOriginalName();
        $directory = 'public/contracts/' . $contractId;
        Storage::makeDirectory($directory);
        return $file->storeAs($directory, $filename);
    }
}
