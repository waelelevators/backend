<?php


namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstallationLocationDetectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'client' => $this->client,
            'stopsNumber' => $this->stopsNumber,
            'elevatorType' => $this->elevatorType,
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'createdBy' => $this->user->name,
            'detectionBy' => $this->detectionBy->name,
            'contractStatus' => $this->contractStatus,
            'status' => $this->status,
            'created_at' => $this->created_at ?? '',
        ];
    }
}
