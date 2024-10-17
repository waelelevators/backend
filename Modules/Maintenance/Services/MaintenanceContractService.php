<?php

namespace Modules\Maintenance\Services;

use App\Helpers\ApiHelper;
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
        // $client = ApiHelper::handleAddClient($data);
        // return ($client);
        $data['client_id'] = 10;


        $contractData = array_diff_key($data, array_flip([
            'start_date',
            'end_date',
            'visits_count',
            'cost',
            'notes',
            'cancellation_allowance',
            'payment_status',
            'receipt_attachment',
            'contract_attachment'
        ]));



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
        $contractData['user_id'] = 1;

        $contract = $this->maintenanceContractRepository->create($contractData);

        $this->generalLogService::log($contract,  'create', 'Contract created', ['contract' => $contract, 'data' => $contractData, 'user_id' => 1]);




        $detailData = array_intersect_key($data, array_flip([
            'start_date',
            'end_date',
            'visits_count',
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
}
