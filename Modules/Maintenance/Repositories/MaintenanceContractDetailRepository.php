<?php

namespace Modules\Maintenance\Repositories;

use Modules\Maintenance\Entities\MaintenanceContractDetail;

class MaintenanceContractDetailRepository
{
    protected $model;

    public function __construct(MaintenanceContractDetail $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // يمكنك إضافة المزيد من الطرق هنا حسب الحاجة
}
