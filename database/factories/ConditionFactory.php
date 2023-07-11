<?php

namespace Database\Factories;

use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ConditionFactory extends Factory
{
    protected $model = Condition::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'show_in_catalog' => $this->faker->boolean(),
            'sbt_suffix' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
