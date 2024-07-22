<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total',
        // 'discount',
        // 'client_id',
        // 'project_name',
        // 'region_id',
        // 'city_id',
        // 'district',
        // 'street',
        // 'location_data',
        // 'stop_number_id',
        // 'elevator_trip_id',
        // 'elevator_type_id',
        // 'elevator_rail_id',
        // 'number_of_stops',
        // 'elevator_journey',
        // 'elevator_room_id',
        // 'elevator_weight_id',
        // 'machine_type_id',
        // 'machine_warranty',
        // 'machine_load_id',
        // 'machine_speed',
        // 'people_load',
        // 'control_card',
        // 'number_of_stages',
        // 'door_opening_direction_id',
        // 'door_opening_size_id',
        // 'elevator_warranty',
        // 'free_maintenance',
        // 'total_number_of_visits',
        // 'how_did_you_get_to_us',
        // 'contract_status',
        // 'user_id',
        // 'attachment',
        // 'contract_number',
        // 'other_additions',
    ];

    // with client
    protected $with = [
        'locationDetection', 'stage', 'stage', 'elevatorRoom', 'template', 'representatives',
        'DoorSize', 'CabinRailsSize', 'PeopleLoad', 'CounterWeightRailsSize', 'innerDoorType', 'elevatorWarranty',
        'outerDoorSpecifications', 'MachineSpeed', 'MachineWarranty', 'installments', 'EntrancesNumber', 'branch',
        'elevatorType', 'elevatorTrip', 'elevatorRail', 'elevatorRoom', 'elevatorWeight', 'machineType',
        'machineLoad', 'controlCard', 'outerDoorDirections', 'stopsNumbers', 'freeMaintenance', 'createdBy'
    ];

    protected $appends = [
        'city', 'region',
        'is_invoice_created',
        'remaining_cost',
        'paid_amount',
        'more_additions',
        'is_ready_to_start'
       
    ];

    public function representatives()
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Filters the query to include only records that are ready to start.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance.
     * @throws -
     * @return \Illuminate\Database\Eloquent\Builder The modified query builder instance.
     */
    public function scopeReadyToStart($query)
    {
        return $query->join('stages', 'stages.id', '=', 'contracts.stage_id')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('stage_id', 1)
                        ->whereHas('payments', function ($query) {
                            $query->havingRaw('SUM(amount) >= contracts.total * stages.required_percentage / 100');
                        });
                })
                    ->orWhere(function ($query) {
                        $query->where('stage_id', 2)
                            ->whereHas('payments', function ($query) {
                                $query->havingRaw('SUM(amount) >= contracts.total * stages.required_percentage / 100');
                            });
                    })
                    ->orWhere(function ($query) {
                        $query->where('stage_id', 3)
                            ->whereHas('payments', function ($query) {
                                $query->havingRaw('SUM(amount) >= contracts.total * stages.required_percentage / 100');
                            });
                    });
            });
    }

    // calculate remaining cost from total using installment
    public function getRemainingCostAttribute()
    {
        $total = $this->total;
        $payments = $this->payments()->sum('amount');
        return $total - $payments;
    }

    // مجموع المبلغ المدفوع
    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getPaidAmountInStage($stage)
    {
        return $this->payments()->where('stage_id', $stage)->sum('amount');
    }

    public function stageToPay($stage)
    {
        $query = $this->installment();

        if ($stage == 1) {
            $query->where('paid_id', 1);
        } elseif ($stage == 2) {
            $query->where('paid_id', 2);
        } elseif ($stage == 3) {
            $query->whereNotIn('paid_id', [1, 2]);
        } else {
            return 0; // or throw an exception if invalid stage values are unacceptable
        }

        return $query->sum('tax');
    }

    public function isPreviousStagePaid($stage)
    {
        // Ensure the stage is valid and between 2 and 3, since stage 1 has no previous stage
        if ($stage < 2 || $stage > 3) {
            return true; // or throw an exception if invalid stage values are unacceptable
        }

        // Get the previous stage
        $previousStage = $stage - 1;

        // Check if the previous stage is paid
        $previousStagePaidAmount = $this->getPaidAmountInStage($previousStage);
        $previousStageToPay = $this->stageToPay($previousStage);

        // Return true if the previous stage is fully paid, otherwise false
        return $previousStagePaidAmount >= $previousStageToPay;
    }

    public function getRemainingAmountInStage($stage)
    {
        $toPay = $this->stageToPay($stage);

        $paid = $this->getPaidAmountInStage($stage);

        return $toPay - $paid;
    }
    public function getPaidPercentInStage($stage)
    {
        $toPay = $this->stageToPay($stage);

        $paid = $this->getPaidAmountInStage($stage);

        $percent = ($paid / $toPay) * 100;

        return $percent . '%';
    }

    public function getIsInvoiceCreatedAttribute()
    {
        switch ($this->stage_id) {
            case 1:
                return $this->stage_one()->count() > 0;
            case 2:
                return $this->stage_two()->count() > 0;
            case 3:
                return $this->stage_three()->count() > 0;
            default:
                return false;
        }
    }

    public function getMoreAdditionsAttribute()
    {
        $Additions = json_decode($this->other_additions, true);

        foreach ($Additions as $value) {
            // # code...
            $model = Addition::find($value)->get();
        }
        return $model ?? '';
    }
    public function getIsReadyToFirstStageAttribute()
    {
        $requiredPercentage = $this->stage->required_percentage;

        return $this->payments()->sum('amount') >= ($this->total * $requiredPercentage / 100);
    }

    public function getIsReadyToStartAttribute($stage)
    {
        // $requiredPercentage = $this->stage->required_percentage;
        // return $this->payments()->sum('amount') >= ($this->total * $requiredPercentage / 100);

        return  $this->getRemainingAmountInStage($stage) == 0;
    }

    public function getIsReadyToStart($stage)
    {
        return $this->getRemainingAmountInStage($stage) == 0;
    }

    // payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'cost' => 'decimal:2',
        'elevator_type_id' => 'integer',
        'elevator_rail_id' => 'integer',
        'elevator_room_id' => 'integer',
        'elevator_weight_id' => 'integer',
        'machine_type_id' => 'integer',
        'machine_load_id' => 'integer',
        'how_did_you_get_to_us' => 'integer',
        'door_opening_direction_id' => 'integer',
        'door_opening_size_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
    public function elevatorWarranty()
    {
        return $this->belongsTo(ElevatorWarranty::class);
    }

    // installments
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function elevatorType()
    {
        return $this->belongsTo(ElevatorType::class);
    }

    public function elevatorTrip()
    {
        return $this->belongsTo(ElevatorTrip::class);
    }

    public function PeopleLoad()
    {
        return $this->belongsTo(PeopleLoad::class);
    }

    public function MachineSpeed()
    {
        return $this->belongsTo(MachineSpeed::class);
    }

    public function elevatorRail()
    {
        return $this->belongsTo(ElevatorRail::class);
    }

    public function elevatorRoom()
    {
        return $this->belongsTo(ElevatorRoom::class);
    }

    public function elevatorWeight()
    {
        return $this->belongsTo(ElevatorWeight::class);
    }

    public function machineType()
    {
        return $this->belongsTo(MachineType::class);
    }

    public function machineLoad()
    {
        return $this->belongsTo(MachineLoad::class);
    }

    public function machineWarranty()
    {
        return $this->belongsTo(ElevatorWarranty::class);
    }

    public function freeMaintenance()
    {
        return $this->belongsTo(ElevatorWarranty::class, 'free_maintenance_id', 'id');
    }

    public function controlCard()
    {
        return $this->belongsTo(ControlCard::class);
    }
    public function innerDoorType()
    {
        return $this->belongsTo(InnerDoorType::class);
    }
    function stopsNumbers()
    {
        return $this->belongsTo(StopNumber::class, 'stop_number_id');
    }
    public function outerDoorDirections()
    {
        return $this->belongsTo(OuterDoorDirection::class, 'outer_door_direction_id');
    }

    public function EntrancesNumber()
    {
        return $this->belongsTo(EntrancesNumber::class, 'entrances_number_id');
    }

    public function getCityAttribute()
    {
        return City::where('id', $this->location_data['city'] ?? 0)->first();
    }

    public function getRegionAttribute()
    {
        return Region::where('id', $this->location_data['region'] ?? 0)->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locationDetection()
    {
        return $this->belongsTo(InstallationLocationDetection::class, 'location_id', 'id');
    }

    /**
     * Get all of the installment for the Contract
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function installment(): HasMany
    {
        return $this->hasMany(Installment::class);
    }
    public function assignments(): HasMany
    {
        return $this->hasMany(LocationAssignment::class);
    }


    // get quotations where stage = 1
    function stage_one()
    {
        return $this->hasMany(Quotation::class, 'contract_id')->where('stage', 1);
    }

    // get quotations where stage = 2
    function stage_two()
    {
        return $this->hasMany(Quotation::class, 'contract_id')->where('stage', 2);
    }

    // get quotations where stage = 3
    function stage_three()
    {
        return $this->hasMany(Quotation::class, 'contract_id')->where('stage', 3);
    }


    // stage
    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // has many outer_door_specifications
    public function outerDoorSpecifications()
    {
        return $this->hasMany(OuterDoorSpecification::class);
    }

    // counterweight_rails_size_id

    public function CounterWeightRailsSize()
    {
        return $this->belongsTo(CounterweightRailsSize::class, 'counterweight_rails_size_id');
    }

    // cabin_rails_size_id
    public function CabinRailsSize()
    {
        return $this->belongsTo(CounterweightRailsSize::class, 'cabin_rails_size_id');
    }

    // door_size

    public function DoorSize()
    {
        return $this->belongsTo(DoorSize::class, 'door_size_id');
    }

    public function external()
    {
        return $this->hasOne(ExternalDoorManufacturer::class);
        //return $this->belongsTo(ExternalDoorManufacturer::class, 'contract_id');
    }

    public function cabin()
    {
        return $this->hasOne(CabinManufacture::class);
    }
    public function internal()
    {
        return $this->hasOne(InternalDoorManufacturer::class);
    }
    public function getExternalStatusAttribute()
    {
        // $coverStatus = $this->external()->orderBy('created_at', 'desc')->first();
        $coverStatus = $this->external()->latest('created_at')->exists();
        return $coverStatus ? 1 : 0;
    }
    public function getCabinStatusAttribute()
    {

        $coverStatus = $this->cabin()->latest('created_at')->exists();
        return $coverStatus ? 1 : 0;
    }
    public function getInternalStatusAttribute()
    {

        $coverStatus = $this->internal()->latest('created_at')->exists();
        return $coverStatus ? 1 : 0;
    }


    /**
     * Get all of the workOrders for the Contract
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function workOrders(): HasMany
    // {
    //     return $this->hasMany(WorkOrder::class);
    // }

    // // اذا كان هنالك امر عمل بنفس مرحله العقد التي تم انشاءها

    // public function getHasWorkOrderAttribute()
    // {
    //     if ($this->workOrders()->where('stage_id', $this->stage_id)->exists()) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    //getNamesAttribute
    // public function getRepresentativesAttribute()
    // {
    //     if ($this->how_did_you_get_to_us == 5)
    //         return Representative::where('contract_id', $this->id ?? 0)->get();
    //     else
    //         return  Representative::where('contract_id', $this->id ?? 0)->first();
    // }

    // public function representatives()
    // {
    //     return $this->hasOne(Representative::class)
    //         ->where('contract_type', 'installments');
    // }

    // public function assig()
    // {
    //     // return 1;
    //     return $this->hasOne(LocationAssignment::class)
    //         ->where('stage_id', $this->stage_id);
    // }
}
