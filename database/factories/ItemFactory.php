<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->word(),
            'supplier_key' => $this->faker->word(),
            'description' => $this->faker->text(),
            'group' => $this->faker->word(),
            'manufacturer' => $this->faker->word(),
            'sbt_item' => $this->faker->word(),
            'image_path' => $this->faker->word(),
            'image_name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
