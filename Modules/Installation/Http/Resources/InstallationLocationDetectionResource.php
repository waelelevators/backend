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
            'client' => $this->client ?? null,
            'stopsNumber' => $this->stopsNumber ?? null,
            'elevatorType' => $this->elevatorType ?? null,
            'city' => $this->city ?? null,
            'neighborhood' => $this->neighborhood ?? null,
            'createdBy' => $this->user->name ?? null,
            'detectionBy' => $this->detectionBy->name ?? null,
            'contractStatus' => $this->contractStatus ?? null,
            'status' => $this->status ?? null,
            // 'created_at' => $this->created_at->format('Y-m-d') ?? null,
        ];
    }
}