<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::truncate();

        $csvFile = fopen(base_path('database/data/locations.csv'), 'r');

        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstLine) {
                Location::create([
                    'sbt_loctid' => $data['0'],
                    'name' => $data['1'],
                    'address1' => $data['2'],
                    'address2' => $data['3'],
                    'city' => $data['4'],
                    'state' => $data['5'],
                    'zip' => $data['6'],
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }
}
