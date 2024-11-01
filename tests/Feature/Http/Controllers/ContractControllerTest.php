<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\City;
use App\Models\Client;
use App\Models\Contract;
use App\Models\DoorOpeningDirection;
use App\Models\DoorOpeningSize;
use App\Models\ElevatorRail;
use App\Models\ElevatorRoom;
use App\Models\ElevatorType;
use App\Models\ElevatorWeight;
use App\Models\MachineLoad;
use App\Models\MachineType;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ContractController
 */
class ContractControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected()
    {
        $contracts = Contract::factory()->count(3)->create();

        $response = $this->get(route('contract.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ContractController::class,
            'store',
            \App\Http\Requests\ContractStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
        $cost = $this->faker->randomFloat(/** decimal_attributes **/);
        $client = Client::factory()->create();
        $project_name = $this->faker->word;
        $region = Region::factory()->create();
        $city = City::factory()->create();
        $district = $this->faker->word;
        $street = $this->faker->streetName;
        $elevator_type = ElevatorType::factory()->create();
        $elevator_rail = ElevatorRail::factory()->create();
        $number_of_stops = $this->faker->numberBetween(-10000, 10000);
        $elevator_journey = $this->faker->numberBetween(-10000, 10000);
        $elevator_room = ElevatorRoom::factory()->create();
        $elevator_weight = ElevatorWeight::factory()->create();
        $machine_type = MachineType::factory()->create();
        $machine_warranty = $this->faker->numberBetween(-10000, 10000);
        $machine_load = MachineLoad::factory()->create();
        $machine_speed = $this->faker->word;
        $people_load = $this->faker->numberBetween(-10000, 10000);
        $control_card = $this->faker->word;
        $number_of_stages = $this->faker->numberBetween(-10000, 10000);
        $door_opening_direction = DoorOpeningDirection::factory()->create();
        $door_opening_size = DoorOpeningSize::factory()->create();
        $elevator_warranty = $this->faker->numberBetween(-10000, 10000);
        $free_maintenance = $this->faker->numberBetween(-10000, 10000);
        $total_number_of_visits = $this->faker->numberBetween(-10000, 10000);
        $how_did_you_get_to_us = $this->faker->text;
        $contract_status = $this->faker->randomElement(/** enum_attributes **/);
        $user = User::factory()->create();

        $response = $this->post(route('contract.store'), [
            'cost' => $cost,
            'client_id' => $client->id,
            'project_name' => $project_name,
            'region_id' => $region->id,
            'city_id' => $city->id,
            'district' => $district,
            'street' => $street,
            'elevator_type_id' => $elevator_type->id,
            'elevator_rail_id' => $elevator_rail->id,
            'number_of_stops' => $number_of_stops,
            'elevator_journey' => $elevator_journey,
            'elevator_room_id' => $elevator_room->id,
            'elevator_weight_id' => $elevator_weight->id,
            'machine_type_id' => $machine_type->id,
            'machine_warranty' => $machine_warranty,
            'machine_load_id' => $machine_load->id,
            'machine_speed' => $machine_speed,
            'people_load' => $people_load,
            'control_card' => $control_card,
            'number_of_stages' => $number_of_stages,
            'door_opening_direction_id' => $door_opening_direction->id,
            'door_opening_size_id' => $door_opening_size->id,
            'elevator_warranty' => $elevator_warranty,
            'free_maintenance' => $free_maintenance,
            'total_number_of_visits' => $total_number_of_visits,
            'how_did_you_get_to_us' => $how_did_you_get_to_us,
            'contract_status' => $contract_status,
            'user_id' => $user->id,
        ]);

        $contracts = Contract::query()
            ->where('cost', $cost)
            ->where('client_id', $client->id)
            ->where('project_name', $project_name)
            ->where('region_id', $region->id)
            ->where('city_id', $city->id)
            ->where('district', $district)
            ->where('street', $street)
            ->where('elevator_type_id', $elevator_type->id)
            ->where('elevator_rail_id', $elevator_rail->id)
            ->where('number_of_stops', $number_of_stops)
            ->where('elevator_journey', $elevator_journey)
            ->where('elevator_room_id', $elevator_room->id)
            ->where('elevator_weight_id', $elevator_weight->id)
            ->where('machine_type_id', $machine_type->id)
            ->where('machine_warranty', $machine_warranty)
            ->where('machine_load_id', $machine_load->id)
            ->where('machine_speed', $machine_speed)
            ->where('people_load', $people_load)
            ->where('control_card', $control_card)
            ->where('number_of_stages', $number_of_stages)
            ->where('door_opening_direction_id', $door_opening_direction->id)
            ->where('door_opening_size_id', $door_opening_size->id)
            ->where('elevator_warranty', $elevator_warranty)
            ->where('free_maintenance', $free_maintenance)
            ->where('total_number_of_visits', $total_number_of_visits)
            ->where('how_did_you_get_to_us', $how_did_you_get_to_us)
            ->where('contract_status', $contract_status)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $contracts);
        $contract = $contracts->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected()
    {
        $contract = Contract::factory()->create();

        $response = $this->get(route('contract.show', $contract));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ContractController::class,
            'update',
            \App\Http\Requests\ContractUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $contract = Contract::factory()->create();
        $cost = $this->faker->randomFloat(/** decimal_attributes **/);
        $client = Client::factory()->create();
        $project_name = $this->faker->word;
        $region = Region::factory()->create();
        $city = City::factory()->create();
        $district = $this->faker->word;
        $street = $this->faker->streetName;
        $elevator_type = ElevatorType::factory()->create();
        $elevator_rail = ElevatorRail::factory()->create();
        $number_of_stops = $this->faker->numberBetween(-10000, 10000);
        $elevator_journey = $this->faker->numberBetween(-10000, 10000);
        $elevator_room = ElevatorRoom::factory()->create();
        $elevator_weight = ElevatorWeight::factory()->create();
        $machine_type = MachineType::factory()->create();
        $machine_warranty = $this->faker->numberBetween(-10000, 10000);
        $machine_load = MachineLoad::factory()->create();
        $machine_speed = $this->faker->word;
        $people_load = $this->faker->numberBetween(-10000, 10000);
        $control_card = $this->faker->word;
        $number_of_stages = $this->faker->numberBetween(-10000, 10000);
        $door_opening_direction = DoorOpeningDirection::factory()->create();
        $door_opening_size = DoorOpeningSize::factory()->create();
        $elevator_warranty = $this->faker->numberBetween(-10000, 10000);
        $free_maintenance = $this->faker->numberBetween(-10000, 10000);
        $total_number_of_visits = $this->faker->numberBetween(-10000, 10000);
        $how_did_you_get_to_us = $this->faker->text;
        $contract_status = $this->faker->randomElement(/** enum_attributes **/);
        $user = User::factory()->create();

        $response = $this->put(route('contract.update', $contract), [
            'cost' => $cost,
            'client_id' => $client->id,
            'project_name' => $project_name,
            'region_id' => $region->id,
            'city_id' => $city->id,
            'district' => $district,
            'street' => $street,
            'elevator_type_id' => $elevator_type->id,
            'elevator_rail_id' => $elevator_rail->id,
            'number_of_stops' => $number_of_stops,
            'elevator_journey' => $elevator_journey,
            'elevator_room_id' => $elevator_room->id,
            'elevator_weight_id' => $elevator_weight->id,
            'machine_type_id' => $machine_type->id,
            'machine_warranty' => $machine_warranty,
            'machine_load_id' => $machine_load->id,
            'machine_speed' => $machine_speed,
            'people_load' => $people_load,
            'control_card' => $control_card,
            'number_of_stages' => $number_of_stages,
            'door_opening_direction_id' => $door_opening_direction->id,
            'door_opening_size_id' => $door_opening_size->id,
            'elevator_warranty' => $elevator_warranty,
            'free_maintenance' => $free_maintenance,
            'total_number_of_visits' => $total_number_of_visits,
            'how_did_you_get_to_us' => $how_did_you_get_to_us,
            'contract_status' => $contract_status,
            'user_id' => $user->id,
        ]);

        $contract->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($cost, $contract->cost);
        $this->assertEquals($client->id, $contract->client_id);
        $this->assertEquals($project_name, $contract->project_name);
        $this->assertEquals($region->id, $contract->region_id);
        $this->assertEquals($city->id, $contract->city_id);
        $this->assertEquals($district, $contract->district);
        $this->assertEquals($street, $contract->street);
        $this->assertEquals($elevator_type->id, $contract->elevator_type_id);
        $this->assertEquals($elevator_rail->id, $contract->elevator_rail_id);
        $this->assertEquals($number_of_stops, $contract->number_of_stops);
        $this->assertEquals($elevator_journey, $contract->elevator_journey);
        $this->assertEquals($elevator_room->id, $contract->elevator_room_id);
        $this->assertEquals($elevator_weight->id, $contract->elevator_weight_id);
        $this->assertEquals($machine_type->id, $contract->machine_type_id);
        $this->assertEquals($machine_warranty, $contract->machine_warranty);
        $this->assertEquals($machine_load->id, $contract->machine_load_id);
        $this->assertEquals($machine_speed, $contract->machine_speed);
        $this->assertEquals($people_load, $contract->people_load);
        $this->assertEquals($control_card, $contract->control_card);
        $this->assertEquals($number_of_stages, $contract->number_of_stages);
        $this->assertEquals($door_opening_direction->id, $contract->door_opening_direction_id);
        $this->assertEquals($door_opening_size->id, $contract->door_opening_size_id);
        $this->assertEquals($elevator_warranty, $contract->elevator_warranty);
        $this->assertEquals($free_maintenance, $contract->free_maintenance);
        $this->assertEquals($total_number_of_visits, $contract->total_number_of_visits);
        $this->assertEquals($how_did_you_get_to_us, $contract->how_did_you_get_to_us);
        $this->assertEquals($contract_status, $contract->contract_status);
        $this->assertEquals($user->id, $contract->user_id);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with()
    {
        $contract = Contract::factory()->create();

        $response = $this->delete(route('contract.destroy', $contract));

        $response->assertNoContent();

        $this->assertDeleted($contract);
    }
}
