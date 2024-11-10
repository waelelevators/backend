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
<<<<<<< HEAD
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
=======

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
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
