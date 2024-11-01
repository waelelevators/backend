<?php

namespace Modules\Maintenance\Repositories;

use Modules\Maintenance\Entities\MaintenanceContract;

class MaintenanceContractRepository
{
    protected $model;

    public function __construct(MaintenanceContract $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // يمكنك إضافة المزيد من الطرق هنا حسب الحاجة
}
