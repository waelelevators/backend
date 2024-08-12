<?php


namespace Modules\Installation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CoveringResource extends JsonResource
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
            'contractNumber' => $this->contract_number,
            'client' => $this->locationDetection->client,
            'elevatorType' => $this->elevatorType,
            'stopsNumber' => $this->stopsNumbers,

            'city' => $this->locationDetection->city,
            'neighborhood' => $this->locationDetection->neighborhood,
            'stage' => $this->stage,
            'openingDirection' => $this->outerDoorDirections,
            'doorSize' => $this->doorSize,
            'doorNumbers' => $this->doors_number,
            'stageAmount' => $this->stageToPay($this->stage_id),
            'remainingStage' => $this->getRemainingAmountInStage($this->stage_id),
            'createdAt' => $this->created_at,


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
