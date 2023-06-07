<?php

namespace Database\Seeders;

use App\Models\CharterItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CharterItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CharterItem::truncate();

        $csvFile = fopen(base_path('database/data/items.csv'), 'r');

        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstLine) {
                CharterItem::create([
                    'key' => $data['0'],
                    'supplier_key' => $data['1'],
                    'description' => $data['2'],
                    'group' => $data['3'],
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }
}
