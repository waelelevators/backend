<?php

namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $client = $this?->contract?->locationDetection?->client;

        // if ($client->type == 1) {
        //     $client_name .= $client->data['first_name'] . ' ';
        //     $client_name .= $client->data['second_name'] . ' ';
        //     $client_name .= $client->data['third_name'] . ' ';
        //     $client_name .= $client->data['last_name'];
        // } else $client_name = $client->data->name ?? 'لايوجد';

        return [
            'id' => $this->id,
            'client' => $client,
            'contract_number' => $this->contract->contract_number,
            'stage' => $this->contract->stage,
            'ElevatorType' => $this->contract->elevatorType,
            'StopsNumber' => $this->contract->stopsNumbers,
            'amount' => $this->amount,
            'residual' => $this->contract->remaining_cost,
            'created_at' => $this->created_at,
            'created_by' => $this->user->createdBy ?? '',
            'attachments' => $this->attachments,
        ];
    }
}
