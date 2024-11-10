<?php

namespace Modules\Maintenance\Services;

use Modules\Maintenance\Repositories\ReportRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ReportService
{
    protected $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }


    public function getAllReports($perPage = 15)
    {
        return $this->reportRepository->getAllReports();
    }


    public function getReportById($id)
    {
        return $this->reportRepository->getReportById($id);
    }

    public function createInitialReport(array $data)
    {
        $data['status'] = 'open'; // الحالة الافتراضية هي "فتح بلاغ"
        return $this->reportRepository->create($data);
    }

    public function assignTechnicianToReport(array $data)
    {
        $data['status'] = 'assigned'; //اسناد فني الصيانه

        return $this->reportRepository->assignTechnicianToReport($data);
    }

    public function addRequiredProductsToReport(array $data)
    {
        return $this->reportRepository->addRequiredProductsToReport($data);
    }


    public function addProblemsToReport(array $data)
    {
        return $this->reportRepository->addProblemsToReport($data);
    }


    // approveReport
    public function approveReport(array $data)
    {
        return $this->reportRepository->approveReport($data);
    }


    public function addProductsToReport(array $data)
    {
        return $this->reportRepository->addProductsToReport($data);
    }
}