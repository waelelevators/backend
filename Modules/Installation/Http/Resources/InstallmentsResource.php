<?php

namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'contract_id' => $this->contract_id,
            'amount' => $this->amount,
            'tax' => $this->tax,
            'paid_id' => $this->paid_id,
            'paid_amount' => $this->contract->getPaidAmountInStage($this->paid_id),
            'remaining_amount' => $this->contract->getRemainingAmountInStage($this->paid_id),
            // Add any other fields you want to include in the response
        ];
    }
}
