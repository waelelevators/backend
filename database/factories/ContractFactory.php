<?php

namespace Database\Factories;

use App\Models\DoorSize;
use App\Models\ElevatorTrip;
use App\Models\EntrancesNumber;
use App\Models\MachineSpeed;
use App\Models\MachineWarranty;
use App\Models\StopNumber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\City;
use App\Models\Client;
use App\Models\Contract;
use App\Models\ControlCard;
use App\Models\DoorOpeningDirection;
use App\Models\DoorOpeningSize;
use App\Models\ElevatorRail;
use App\Models\ElevatorRoom;
use App\Models\ElevatorType;
use App\Models\ElevatorWeight;
use App\Models\MachineLoad;
use App\Models\MachineType;
use App\Models\PeopleLoad;
use App\Models\Region;
use App\Models\User;
use backend\models\RailsTypes;

class ContractFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => 1,
            'total' => $this->faker->randomFloat(2, 0, 999999.99),
            'tax' => $this->faker->randomFloat(2, 0, 999999.99),
            'project_name' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'elevator_type_id' => ElevatorType::factory(),
            'cabin_rails_size_id' => ElevatorRail::factory(),
            'counterweight_rails_size_id' => ElevatorRail::factory(),
            'stop_number_id' => StopNumber::factory(),
            'elevator_trip_id' => ElevatorTrip::factory(),
            'elevator_room_id' => ElevatorRoom::factory(),
            'machine_type_id' => MachineType::factory(),
            'machine_warranty_id' => MachineWarranty::factory(),
            'machine_load_id' => MachineLoad::factory(),
            'machine_speed_id' => MachineSpeed::factory(),
            'people_load_id' => PeopleLoad::factory(),
            'control_card_id' => ControlCard::factory(),
            'entrances_number_id' => EntrancesNumber::factory(),
            'outer_door_direction_id' => EntrancesNumber::factory(),
            'inner_door_type_id' => EntrancesNumber::factory(),
            'door_size_id' => DoorSize::factory(),


            'client_id' => Client::factory(),
            'region_id' => Region::factory(),
            'city_id' => City::factory(),
            'district' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'street' => $this->faker->streetName,


            'elevator_weight_id' => ElevatorWeight::factory(),
            'number_of_stages' => $this->faker->numberBetween(-10000, 10000),
            'door_opening_direction_id' => DoorOpeningDirection::factory(),
            'elevator_warranty' => $this->faker->numberBetween(-10000, 10000),
            'free_maintenance' => $this->faker->numberBetween(-10000, 10000),
            'total_number_of_visits' => $this->faker->numberBetween(-10000, 10000),
            'how_did_you_get_to_us' => $this->faker->text,
            'contract_status' => $this->faker->randomElement(["Draft", "Completed", "Other"]),
            'user_id' => User::factory(),
        ];
    }
}
