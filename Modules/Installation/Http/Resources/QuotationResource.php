<?php


namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
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
            'q_number' => $this->q_number,
            'region' => $this->region->name,
            'city' => $this->city->name,
            'neighborhood' => $this->location_data['neighborhood'],
            'elevatorType' => $this->elevator_type,
            'stopsNumber' => $this->stops_number,
            'client' => $this->client,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at,
        ];
    }
}
