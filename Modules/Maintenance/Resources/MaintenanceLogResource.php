<?php

namespace Modules\Maintenance\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'm_info' =>  $this->mInfo,
            'area_id' =>  $this->area_id,
            'currentContract' => $this->currentContract,

           
            
            // 'buildingType' => $this->mInfo->building_type,
            // 'elevatorType' => $this->mInfo->elevator_type,
         
        ];
    }
}
