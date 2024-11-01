<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ContractTechnician;

class ContractTechnicianFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContractTechnician::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'technician_id' => $this->faker->numberBetween(-10000, 10000),
            'contract_id' => $this->faker->numberBetween(-10000, 10000),
        ];
    }
}
