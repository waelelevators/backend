<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Contract;
use App\Models\ContractStage;

class ContractStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContractStage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contract_id' => Contract::factory(),
            'stage_id' => $this->faker->numberBetween(-100000, 100000),
            'amount' => $this->faker->numberBetween(-100000, 100000),
            'tax' => $this->faker->numberBetween(-100000, 100000),
        ];
    }
}
