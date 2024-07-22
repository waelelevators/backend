<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\OuterDoorSpecification;

class OuterDoorSpecificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OuterDoorSpecification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contract_id' => $this->faker->numberBetween(-10000, 10000),
            'floor' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'number_of_doors' => $this->faker->numberBetween(-10000, 10000),
            'out_door_specification' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'door_opening_direction' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'out_door_specification_tow' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'door_opening_direction_tow' => $this->faker->regexify('[A-Za-z0-9]{255}'),
        ];
    }
}
