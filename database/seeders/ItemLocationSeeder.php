<?php

namespace Database\Seeders;

use App\Models\ItemLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemLocationSeeder extends Seeder
{
    public function run(): void
    {

        $icilocs = DB::connection('basilisk')->table('iciloc80')
            ->where('lonhand', '>', 0)->get;

        foreach ($iclocs as $iciloc) {

        }
    }
}
