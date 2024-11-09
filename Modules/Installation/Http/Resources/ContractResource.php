<?php


namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
<<<<<<< HEAD
            'isCompleteStage' => $this->is_complete_stage ?? null,
            'contractNumber' => $this->contract_number ?? null,
            'clientName' => $this->locationDetection->client->name ?? null,
            'idNumber' => $this->locationDetection->client->id_number ?? null,
            'phone' => $this->locationDetection->client->phone ?? null,
            'elevatorType' => $this->elevatorType->name ?? null,
            'stopsNumber' => $this->stopsNumbers->name ?? null,
            'contractStatus' => $this->contract_status ?? null,
            'city' => $this->locationDetection->city->name ?? null,
            'neighborhood' => $this->locationDetection->neighborhood->name ?? null,
            'stage' => $this->stage->name ?? null,
            'total' => $this->total ?? null,
            'remainingCost' => $this->remaining_cost ?? null,
            'discount' => $this->discount ?? null,
            'tax' => $this->tax ?? null,
            'createdAt' => $this->created_at ?? null,
=======
            'isCompleteStage' => $this->is_complete_stage,
            'contractNumber' => $this->contract_number,
            'clientName' => $this->locationDetection->client->name,
            'idNumber' => $this->locationDetection->client->id_number,
            'phone' => $this->locationDetection->client->phone,
            'elevatorType' => $this->elevatorType->name,
            'stopsNumber' => $this->stopsNumbers->name,
            'contractStatus' => $this->contract_status,
            'city' => $this->locationDetection->city->name,
            'neighborhood' => $this->locationDetection->neighborhood->name,
            'stage' => $this->stage->name,
            'total' => $this->total,
            'remainingCost' => $this->remaining_cost,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'createdAt' => $this->created_at,
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7


            // 'locationDetection' => $this->locationDetection,
            // 'project_name' => $this->project_name,
            // 'location_data' => $this->location_data,

            //


            // 'elevator_trip' => $this->elevatorTrip,
            // 'elevator_journey' => $this->elevator_journey,
            // 'elevator_room' => $this->elevatorRoom,
            // 'elevator_weight' => $this->elevatorWeight,
            // 'machine_type' => $this->machineType,
            // 'machine_warranty' => $this->machineWarranty,
            // 'machine_load_id' => $this->machine_load_id,
            // 'machine_speed' => $this->machine_speed,
            // 'people_load' => $this->people_load,
            // 'control_card' => $this->control_card,
            // 'number_of_stages' => $this->number_of_stages,
            // 'door_opening_direction_id' => $this->door_opening_direction_id,
            // 'door_opening_size_id' => $this->door_opening_size_id,
            // 'elevator_warranty' => $this->elevator_warranty,
            // 'free_maintenance' => $this->free_maintenance,
            // 'total_number_of_visits' => $this->total_number_of_visits,
            // 'how_did_you_get_to_us' => $this->how_did_you_get_to_us,
            // 'attachment' => $this->attachment,
            // 'user_id' => $this->user_id,
            // //  'payments' => $this->payments,

            // 'is_invoice_created' => $this->is_invoice_created,
            // 'controlCard' => $this->controlCard,
            // 'more_additions' => $this->more_additions,

            // 'is_ready_to_start' => $this->is_ready_to_start ?? false,
            // 'paid_amount' => $this->paid_amount,
            // 'has_work_order' => $this->has_work_order,

        ];
    }
}