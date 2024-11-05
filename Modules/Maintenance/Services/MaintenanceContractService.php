<?php

namespace Modules\Maintenance\Services;

use App\Helpers\ApiHelper;
use App\Models\Branch;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractDetail;
use App\Models\MaintenanceVisit;
use App\Service\GeneralLogService;
use Modules\Maintenance\Repositories\MaintenanceContractRepository;
use Modules\Maintenance\Repositories\MaintenanceContractDetailRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Maintenance\Entities\MaintenanceContract as EntitiesMaintenanceContract;

class MaintenanceContractService
{
    protected $maintenanceContractRepository;
    protected $maintenanceContractDetailRepository;
    protected $generalLogService;

    protected $maintenance_types = ['free', 'pid', 'external', 'upgrade', 'renewal', 'other'];

    public function __construct(
        MaintenanceContractRepository $maintenanceContractRepository,
        MaintenanceContractDetailRepository $maintenanceContractDetailRepository,
        GeneralLogService $generalLogService
    ) {
        $this->maintenanceContractRepository = $maintenanceContractRepository;
        $this->maintenanceContractDetailRepository = $maintenanceContractDetailRepository;
        $this->generalLogService = $generalLogService;
    }

    public function createContract(array $data)
    {

        $client = ApiHelper::handleAddClient($data);

        $data['client_id'] = $client->id;

        $user_id = auth('sanctum')->user()->id;
        $representative_id =  ApiHelper::handleGetUsData($data, 'maintenances');



        $contractData = array_diff_key($data, array_flip([
            'notes',
            'cancellation_allowance',
            'payment_status',
            'receipt_attachment',
            'contract_attachment'
        ]));




        // الحمد لله رب العالمين والرحمن الرحيم

        $contract_number = $this->contractCode($data['maintenance_type'] ?? 1, $data['branch_id']) ?? '';

        $contractData['contract_type'] = $data['isDraft'] ? 'draft' : 'contract';
        $contractData['contract_number'] = $contract_number;
        $contractData['user_id'] = $user_id;
        $contractData['control_card_id'] = $data['control_card_id'];
        $contractData['elevator_type_id'] = $data['elevator_type_id'];
        $contractData['total'] = $data['cost'] ?? 0;
        $contractData['representative_id'] = $representative_id ?? 0;
        $contractData['stops_count'] = $data['stops_count'];
        $contractData['machine_type_id'] = $data['machine_type_id'];
        $contractData['drive_type_id'] = $data['drive_type_id'];
        $contractData['machine_speed_id'] = $data['machine_speed_id'];
        $contractData['door_size_id'] = $data['door_size_id'];
        $contractData['branch_id'] = $data['branch_id'];
        $contractData['region_id'] = $data['region_id'];




        $contract = $this->maintenanceContractRepository->create($contractData);

        $this->generalLogService::log($contract,  'create', 'Contract created', ['contract' => $contract, 'data' => $contractData, 'user_id' => 1]);



        $detailData = [];
        $detailData['maintenance_contract_id'] = $contract->id;
        $detailData['client_id'] = $data['client_id'];
        $detailData['user_id'] = $contract->user_id;
        $detailData['remaining_visits'] = $data['visits_count'] ?? 12;
        $detailData['maintenance_type'] = $this->maintenance_type[($data['maintenance_type'] ?? 1) - 1] ?? 'free';
        $detailData['start_date'] = $data['start_date'];
        $detailData['end_date'] = $data['end_date'];
        $detailData['cost'] = $data['cost'];
        $detailData['visits_count'] = $data['visits_count'];



        if ($data['isDraft'] !== true) {

            $detail = $this->maintenanceContractDetailRepository->create($detailData);
            $contract->update(['active_contract_id' => $detail->id]);
            $this->createVisits($detail);
            $this->generalLogService::log($detail, 'create', 'Contract detail created', ['data' => $detailData, 'user_id' => 1]);
        }


        return $contract->load('contractDetail');
    }


    // create visit
    public function createVisits($data)
    {


        $startDate = Carbon::parse($data['start_date']);
        $visitData = [];

        for ($i = 0; $i < $data['visits_count']; $i++) {
            $visitDate = $startDate->copy()->addMonths($i);
            $visitData[] = [
                'maintenance_contract_id' => $data['maintenance_contract_id'],
                'visit_date' => $visitDate->format('Y-m-d'),
                'user_id' => 1,
                'maintenance_contract_detail_id' => $data['id'],
                'status' => 'scheduled',
                'technician_id' => 0,
            ];
        }

        return MaintenanceVisit::insert($visitData);
    }

    // convert draft to contract
    public function convertDraftToContract($data)
    {

        $contract = MaintenanceContract::findOrFail($data['contract_id']);
        $data['contract_type'] = 'contract';
        $contract->update($data);

        // create contract detail
        $contractDetail = $this->createContractDetail($contract, $data);

        // create visits
        $this->createVisits([
            'id' => $contractDetail->id,
            'maintenance_contract_id' => $contract->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'visits_count' => $data['visits_count'],
        ]);


        // ahmed hmed yousif
        return $contract;
    }




    public function createContractDetail($contract, $data)
    {
        $user_id = auth('sanctum')->user()->id;

        $contractData = [
            'maintenance_contract_id' => $contract->id,
            'installation_contract_id' => $contract->installation_contract_id,
            'client_id' => $contract->client_id,
            'user_id' => $user_id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'visits_count' => $data['visits_count'],
            'cost' => $contract->cost,
            'remaining_visits' => $data['visits_count'],
            'cancellation_allowance' => 1,
            'const' => $data['cost'],
        ];
        return MaintenanceContractDetail::create($contractData);
    }


    // contractcode
    public function contractCode($maintenance_type, $barch_id)
    {
        $barch = Branch::find($barch_id);

        $code  = $barch->prefix;
        $last_id = $barch->last_maintenance_id;
        $contractCode = '';

        if ($this->maintenance_types[$maintenance_type - 1] != 'external') {
            $contractCode .= $code . '-' . $last_id;
        } else {
            $contractCode .= 'EXT-' . $code . '-' . $last_id;
        }
        $barch->last_maintenance_id = $last_id + 1; // increment last id by 1 for next contract
        $barch->save();
        return $contractCode;
    }


    // updateDraftContract
    public function updateDraftContract($data)
    {
        $data['total'] = $data['cost'];
        $data['control_type_id'] = $data['control_card_id'] ?? null;
        $contract = MaintenanceContract::findOrFail($data['contract_id']);

        $contract->update($data);
        return $contract;
    }

    // updateContract
    public function updateContract($data)
    {



        $data['total'] = $data['cost'] ?? 0;

        $contract = EntitiesMaintenanceContract::findOrFail($data['contract_id']);

        $contract->update($data);

        $this->generalLogService::log($contract, 'update', 'Contract updated', ['contract' => $contract, 'data' => $data, 'user_id' => auth('sanctum')->user()->id]);

        $contractDetail = $contract->activeContract();
        // update contract detail
        $detailData = [
            'installation_contract_id' => $data['installation_contract_id'] ?? $contract->installation_contract_id ?? null,
            'maintenance_contract_id' => $data['maintenance_contract_id'] ?? $contract->maintenance_contract_id ?? null,
            'maintenance_type' => $data['maintenance_type'] ?? $contract->maintenance_type ?? null,
            'client_id' => $data['client_id'] ?? $contract->client_id ?? null,
            'start_date' => $data['start_date'] ?? $contract->start_date ?? null,
            'end_date' => $data['end_date'] ?? $contract->end_date ?? null,
            'visits_count' => $data['visits_count'] ?? $contract->visits_count ?? null,
            'cost' => $data['cost'] ?? $contract->cost ?? null,
            'notes' => $data['notes'] ?? $contract->notes ?? null,
            'remaining_visits' => $data['remaining_visits'] ?? $contract->remaining_visits ?? null,
            'cancellation_allowance' => $data['cancellation_allowance'] ?? $contract->cancellation_allowance ?? null,
        ];



        $contractDetail->update($detailData);
        return $contract;
    }

    // endContract
    public function endContract($id)
    {
        $contract = MaintenanceContract::findOrFail($id);
        $contract->activeContract()->update(['status' => 'expired']);
        return $contract;
    }



    // renewContract($request->all(), $id)
    public function renewContract($data, $id)
    {
        $user_id = auth('sanctum')->user()->id;

        // جب لبيانات من قااعده البيانات
        $contract = MaintenanceContract::with('activeContract')->findOrFail($id);
        $activeContract = MaintenanceContractDetail::find($contract->active_contract_id);


        // Merge $data with $activeContract, removing any key that is in $data
        $activeContractData = $activeContract->toArray();
        $mergedData = array_merge($activeContractData, $data);
        $mergedData['remaining_visits'] = $data['visits_count'];


        if ($activeContractData['maintenance_type'] === 'free') {
            $mergedData['maintenance_type'] = 'paid';
        }



        // Prepare new data for contract detail
        $newData = array_merge($mergedData, [
            'remaining_visits' => $data['visits_count'],
            'installation_contract_id' => $contract->installation_contract_id,
            'maintenance_contract_id' => $contract->id,
            'client_id' => $contract->client_id,
            'user_id' => auth('sanctum')->user()->id,
            'status' => 'active',
        ]);

        unset($contractDetails['id'], $contractDetails['created_at'], $contractDetails['updated_at']);


        $contractDetail = new MaintenanceContractDetail();
        $newContractDetail = $contractDetail->create($newData);

        $this->generalLogService::log($newContractDetail, 'create', 'Contract detail created', ['data' => $newContractDetail, 'user_id' => $user_id]);

        $contract->active_contract_id = $newContractDetail->id;
        $contract->save();

        $this->generalLogService::log($newContractDetail, 'create', 'Contract detail created', ['data' => $newContractDetail, 'user_id' => $user_id]);

        // create visits
        $this->createVisits([
            'id' => $newContractDetail->id,
            'maintenance_contract_id' => $contract->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'visits_count' => $data['visits_count'],
        ]);



        return MaintenanceContract::with('activeContract', 'activeContract.visits')->findOrFail($id);
    }


    // uploadFiles
    public function uploadFiles(Request $request)
    {
        $contract = MaintenanceContract::findOrFail($request->maintenance_contract_id);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $file->storeAs('public/contracts/' . $contract->id, $filename);

        $contract->file = $filename;
        $contract->save();

        return $contract;
    }
}
