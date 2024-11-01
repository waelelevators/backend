<?php

namespace Modules\Maintenance\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MaintenanceInfoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
