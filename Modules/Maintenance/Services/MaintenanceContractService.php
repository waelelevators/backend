<?php

namespace Modules\Maintenance\Services;

use App\Helpers\ApiHelper;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractDetail;
use App\Models\MaintenanceVisit;
use App\Service\GeneralLogService;
use Modules\Maintenance\Repositories\MaintenanceContractRepository;
use Modules\Maintenance\Repositories\MaintenanceContractDetailRepository;
use Carbon\Carbon;

class MaintenanceContractService
{
    protected $maintenanceContractRepository;
    protected $maintenanceContractDetailRepository;
    protected $generalLogService;

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
<<<<<<< HEAD
        $client = ApiHelper::handleAddClient($data);
        // return ($client);
        $data['client_id'] = $client->id;
        // return auth('sanctum')->user();
        $user_id = auth('sanctum')->user()->id;


=======
        $user_id = auth('sanctum')->user()->id;


        // اضافة عميل
        $client = ApiHelper::handleAddClient($data);
        $data['client_id'] = $client->id;


>>>>>>> 1ebb111 (Maintenance Part)
        $contractData = array_diff_key($data, array_flip([
            // 'start_date',
            // 'end_date',
            'visits_count',
            'cost',
            'notes',
            'cancellation_allowance',
            'payment_status',
            'receipt_attachment',
            'contract_attachment'
        ]));


<<<<<<< HEAD
        $contractData['contract_type'] = $data['isDraft'] ? 'draft' : 'contract';
        $contractData['user_id'] = $user_id;

        // dd($contractData);

        $contract = $this->maintenanceContractRepository->create($contractData);

        $this->generalLogService::log($contract,  'create', 'Contract created', ['contract' => $contract, 'data' => $contractData, 'user_id' => 1]);




        $detailData = array_intersect_key($data, array_flip([
            // 'start_date',
            // 'end_date',
            'visits_count',
=======

        // if (isset($data['site_images']) && is_array($data['site_images'])) {
        //     $imagesPaths = [];
        //     foreach ($data['site_images'] as $image) {
        //         $path = $image->store('site_images', 'public');
        //         $imagesPaths[] = $path;
        //     }
        //     $contractData['site_images'] = json_encode($imagesPaths);
        // }

        $contractData['contract_number'] = 'M-' . date('Y-m-d') . '-' . rand(1000, 9999);
        $contractData['contract_type'] = $data['isDraft'] ? 'draft' : 'contract';
        $contractData['user_id'] = $user_id;

        $contract = $this->maintenanceContractRepository->create($contractData);

        $this->generalLogService::log($contract,  'create', 'Contract created', ['contract' => $contract, 'data' => $contractData, 'user_id' => $user_id]);


        $detailData = array_intersect_key($data, array_flip([
            'start_date',
            'end_date',
            'visits_count',
            'visits_count',
            'maintenance_type',
>>>>>>> 1ebb111 (Maintenance Part)
            'cost',
            'notes',
            'cancellation_allowance',
            'payment_status',
            'receipt_attachment',
            'contract_attachment'
        ]));

        $detailData['maintenance_contract_id'] = $contract->id;
        // $detailData['installation_contract_id'] = $contract->installation_contract_id;
        $detailData['client_id'] = $data['client_id'];
        $detailData['user_id'] = $contract->user_id;
        $detailData['remaining_visits'] = $detailData['visits_count'];

        // dd($detailData);
        // رقم الكوتيشن
<<<<<<< HEAD
        $quotation_number = "Q-" . date('Y-m-d') . "-" . rand(1000, 9999);
=======
        $quotation_number = "Q-" . date('Y') . "-" . rand(1000, 9999);
>>>>>>> 1ebb111 (Maintenance Part)

        if ($data['isDraft'] !== true) {

            $detail = $this->maintenanceContractDetailRepository->create($detailData);
<<<<<<< HEAD
            $contract->update(['active_contract_id' => $detail->id, 'quotation_number' => $quotation_number]);
            $this->createVisits($detail);
            $this->generalLogService::log($detail, 'create', 'Contract detail created', ['data' => $detailData, 'user_id' => 1]);
=======
            $contract->update(['active_contract_id' => $detail->id]);
            $this->createVisits($detail);
            $this->generalLogService::log($detail, 'create', 'Contract detail created', ['data' => $detailData, 'user_id' => auth('sanctum')->user()->id]);
>>>>>>> 1ebb111 (Maintenance Part)
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


        $quotation_number = "Q-" . date('Y-m-d') . "-" . rand(1000, 9999);
        $data['contract_number'] = $quotation_number;
        // convert draft to contract contract type most be draft
        $contract = MaintenanceContract::findOrFail($data['contract_id']);
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

        return $contract;
    }

    public function createContractDetail($contract, $data)
    {
        $user_id = auth('sanctum')->user()->id;
        // maintenance_contract_details `id`, `installation_contract_id`, `maintenance_contract_id`, `client_id`, `user_id`, `start_date`, `end_date`,
        //  `visits_count`, `cost`, `notes`, `remaining_visits`, `cancellation_allowance`, `payment_status`, `receipt_attachment`, `contract_attachment`, `created_at`, `updated_at`
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
        ];
        return MaintenanceContractDetail::create($contractData);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 1ebb111 (Maintenance Part)
