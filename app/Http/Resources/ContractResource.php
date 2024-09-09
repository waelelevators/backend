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
            'name' => $this->locationDetection->client->name,
            'phone' => $this->locationDetection->client->phone,
            'project_name' => $this->project_name,
            'city' => $this->locationDetection->city->name,
            'region' => $this->locationDetection->region->name,
            'neighborhood' => $this->locationDetection->neighborhood->name,
            'elevatorType' => $this->elevatorType->name,
            'stage' => $this->stage->name,
            'elevator_trip' => $this->elevatorTrip->name,
            'stops_numbers' => $this->stopsNumbers->name,
            'elevator_room' => $this->elevatorRoom->name,
            'machine_type' => $this->machineType->name,
            'machine_warranty' => $this->machineWarranty->name,
            'machine_load_id' => $this->machine_load_id,
            'machine_speed' => $this->machineSpeed->name,
            'people_load' => $this->people_load,
            'number_of_stages' => $this->number_of_stages,
            'door_opening_direction_id' => $this->door_opening_direction_id,
            'door_opening_size_id' => $this->door_opening_size_id,
            'elevator_warranty' => $this->elevator_warranty,
            'free_maintenance' => $this->free_maintenance,
            'total_number_of_visits' => $this->total_number_of_visits,
            'how_did_you_get_to_us' => $this->how_did_you_get_to_us,
            'contract_status' => $this->contract_status,

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
