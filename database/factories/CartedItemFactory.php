<?php

namespace Database\Factories;

use App\Models\CartedItem;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CartedItemFactory extends Factory
{
    protected $model = CartedItem::class;

    public function definition(): array
    {
        return [
            'quantity' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
            'item_id' => Item::factory(),
        ];
    }
}
