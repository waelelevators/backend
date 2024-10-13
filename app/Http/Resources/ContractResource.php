<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */


    // طلبات البضاعة
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total' => $this->total,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'name' => $this->locationDetection->client->name ?? null,
            'phone' => $this->locationDetection->client->phone ?? null,
            'project_name' => $this->project_name ?? null,
            'city' => $this->locationDetection->city->name ?? null,
            'region' => $this->locationDetection->region->name ?? null,
            'neighborhood' => $this->locationDetection->neighborhood->name ?? null,
            'elevatorType' => $this->elevatorType->name ?? null,
            'stage' => $this->stage->name,
            'elevator_trip' => $this->elevatorTrip->name ?? null,
            'stops_numbers' => $this->stopsNumbers->name ?? null,
            'elevator_room' => $this->elevatorRoom->name ?? null,
            'machine_type' => $this->machineType->name ?? null,
            'machine_warranty' => $this->machineWarranty->name ?? null,
            'machine_load_id' => $this->machine_load_id ?? null,
            'machine_speed' => $this->machineSpeed->name ?? null,
            'people_load' => $this->people_load ?? null,
            'number_of_stages' => $this->number_of_stages,
            'door_opening_direction_id' => $this->door_opening_direction_id ?? null,
            'door_opening_size_id' => $this->door_opening_size_id ?? null,
            'elevator_warranty' => $this->elevator_warranty ?? null,
            'free_maintenance' => $this->free_maintenance ?? null,
            'total_number_of_visits' => $this->total_number_of_visits ?? null,
            'how_did_you_get_to_us' => $this->how_did_you_get_to_us ?? null,
            'contract_status' => $this->contract_status ?? null,

            'attachment' => $this->attachment,
            'user_id' => $this->user_id,
            'payments' => $this->payments,
            'remaining_cost' => $this->remaining_cost,
            'is_invoice_created' => $this->is_invoice_created,
            'control_card' => $this->controlCard->name,
            'more_additions' => $this->more_additions,
            'contract_number' => $this->contract_number,
            'is_ready_to_start' => $this->ready_to_start ?? false,
            'paid_amount' => $this->paid_amount,
            'has_work_order' => $this->has_work_order,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}