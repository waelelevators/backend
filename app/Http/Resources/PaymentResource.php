<?php

namespace App\Http\Resources;

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

        $client_name = '';

        // if ($client->type == 1) {
        //     $client_name .= $client->data['first_name'] . ' ';
        //     $client_name .= $client->data['second_name'] . ' ';
        //     $client_name .= $client->data['third_name'] . ' ';
        //     $client_name .= $client->data['last_name'];
        // } else $client_name = $client->data->name ?? 'لايوجد';

        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'attachments' => $this->attachments,
            'client_name' => $client_name,
            'contract' => $this->contract,
            'created_at' => $this->created_at,

        ];
    }
}
