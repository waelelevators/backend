<?php

namespace Modules\Maintenance\Services;

use App\Service\GeneralLogService;
use Modules\Maintenance\Repositories\MaintenanceContractRepository;
use Modules\Maintenance\Repositories\MaintenanceContractDetailRepository;

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

        if (isset($data['site_images']) && is_array($data['site_images'])) {
            $imagesPaths = [];
            foreach ($data['site_images'] as $image) {
                $path = $image->store('site_images', 'public');
                $imagesPaths[] = $path;
            }
            $contractData['site_images'] = json_encode($imagesPaths);
        }

        $contractData['contract_number'] = 'M-' . date('Y-m-d') . '-' . rand(1000, 9999);
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
        $detailData['installation_contract_id'] = $contract->id;
        $detailData['client_id'] = $data['client_id'];
        $detailData['user_id'] = $contract->user_id;
        $detailData['remaining_visits'] = $detailData['visits_count'];

        $detail = $this->maintenanceContractDetailRepository->create($detailData);

        $this->generalLogService::log($detail, 'create', 'Contract detail created', ['data' => $detailData, 'user_id' => 1]);

        return $contract->load('contractDetail');
    }
}
