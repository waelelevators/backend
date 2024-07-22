<?php

namespace Modules\Maintenance\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyResource extends JsonResource
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
            'm_id' => $this->m_id,
            'maintenance' => $this->maintenance,
            'visit_date' => $this->visit_date,
            'started_date' => $this->started_date,
            'ended_date' => $this->ended_date,
            'visitStatus' => $this->visitStatus,
            'notes' => $this->notes,
            'tech_id' => $this->tech_id
        ];
    }
}
