<?php

namespace Database\Factories;

use App\Models\InstallationLocationDetection;
use App\Models\Client;
use App\Models\User;
use App\Models\Representative;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstallationLocationDetectionFactory extends Factory
{
    protected $model = InstallationLocationDetection::class;

    public function definition()
    {
        return [
            'location_data' => [
                'region' => $this->faker->numberBetween(1, 2),
                'city' => $this->faker->numberBetween(1, 3),
                'neighborhood' => $this->faker->numberBetween(1, 166),
            ],
            'well_data' => [
                'well_image' => '',
                'well_height' => $this->faker->numberBetween(1, 5),
                'well_depth' => $this->faker->numberBetween(1, 5),
                'well_width' => $this->faker->numberBetween(1, 5),
                'last_floor_height' => $this->faker->numberBetween(1, 5),
                'bottom_the_elevator' => $this->faker->numberBetween(1, 5),
                'stop_number_id' => $this->faker->numberBetween(1, 5),
                'elevator_trips_id' => $this->faker->numberBetween(1, 5),
                'elevator_type_id' => $this->faker->numberBetween(1, 5),
                'entrances_number_id' => $this->faker->numberBetween(1, 5),
                'well_type' => $this->faker->numberBetween(1, 5),
                'door_open_direction_id' => $this->faker->numberBetween(1, 5),
                'elevator_weight_location_id' => $this->faker->numberBetween(1, 5),
                'weight_cantilever_size_guide' => $this->faker->numberBetween(1, 5),
                'cabin_cantilever_size_guide' => $this->faker->numberBetween(1, 5),
                'dbg_weight' => $this->faker->numberBetween(1, 5),
                'dbg_cabin' => $this->faker->numberBetween(1, 5),
                'cabin_depth' => $this->faker->numberBetween(1, 5),
                'cabin_width' => $this->faker->numberBetween(1, 5),
                'people_load' => $this->faker->numberBetween(1, 5),
                'machine_load' => $this->faker->numberBetween(1, 5),
                'normal_door' => $this->faker->numberBetween(60, 5),
                'center_door' => $this->faker->numberBetween(60, 90),
                'telescope_door' => $this->faker->numberBetween(60, 90),
            ],

            'machine_data' => [
                'machine_room_height' => $this->faker->randomFloat(2, 2, 10),
                'machine_room_width' => $this->faker->randomFloat(2, 2, 10),
                'machine_room_depth' => $this->faker->randomFloat(2, 2, 10),
            ],
            'floor_data' => json_encode([
                'floor_id' => $this->faker->numberBetween(1, 12),
                'well_width' => $this->faker->randomFloat(2, 1, 3),
                'well_depth' => $this->faker->randomFloat(2, 1, 3),
                'right_shoulder_size' => $this->faker->randomFloat(2, 0.5, 2),
                'door_height' => $this->faker->randomFloat(2, 1.8, 2.5),
                'door_size' => $this->faker->randomFloat(2, 0.5, 1.2),
                'left_shoulder_size' => $this->faker->randomFloat(2, 0.5, 2),
                'floor_height' => $this->faker->randomFloat(2, 2, 4),
            ],5),
            'well_type' => 1,
            'client_id' => Client::inRandomOrder()->first()->id,
            'representative_id' =>  Representative::inRandomOrder()->first()->id,
            'detection_by' => User::inRandomOrder()->first()->id,
            'user_id' =>  User::inRandomOrder()->first()->id,
        ];
    }
}
