<?php

namespace Database\Factories;

use App\Models\Condition;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ItemLocationFactory extends Factory
{
    protected $model = ItemLocation::class;

    public function definition(): array
    {
        return [
            'quantity' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'item_id' => Item::factory(),
            'location_id' => Location::factory(),
            'condition_id' => Condition::factory(),
        ];
    }
}
