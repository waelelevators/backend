<?php

namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationAssignmentResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'contract_number' => $this->contract->contract_number,
            'contract_id' => $this->contract->id,
            'client' => $this->contract->locationDetection->client,
            'city' => $this->contract->locationDetection->city,
            'neighborhood' => $this->contract->locationDetection->neighborhood,
            'elevatorType' => $this->contract->locationDetection->elevatorType,
            'stopsNumber' => $this->contract->locationDetection->stopsNumber,
            'representatives' => $this->contract->representatives,
            'stage' => $this->stage,
            'status' => $this->status,
            'financial_status' => $this->financial_status,
            'created_at' => $this->created_at
        ];
    }

    // public function toShow()
    // {
    //     return [
    //         'id' => $this->id,
    //         'location_id' => $this->location_id,
    //         'user_id' => $this->user_id,
    //         'assigned_at' => $this->assigned_at,
    //         'status' => $this->status,
    //         // Add more fields as needed
    //     ];
    // }
}
