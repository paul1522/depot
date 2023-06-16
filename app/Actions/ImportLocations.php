<?php

namespace App\Actions;

use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportLocations
{
    use AsAction;

    public function handle(): void
    {
        $icloct = DB::connection('basilisk')
            ->table('icloct80')
            ->where('loctid', 'like', 'CH/%')
            ->get();

        foreach ($icloct as $loct) {
            Location::firstOrCreate([
                'sbt_loctid' => $loct->loctid,
            ], [
                'name' => trim($loct->locdesc) != '' ? trim($loct->locdesc) : $loct->loctid,
                'address1' => trim($loct->addrs1) != '' ? trim($loct->addrs1) : '---',
                'address2' => trim($loct->addrs2) != '' ? trim($loct->addrs2) : '---',
                'city' => trim($loct->city) != '' ? trim($loct->city) : '---',
                'state' => trim($loct->state) != '' ? trim($loct->state) : '---',
                'zip' => trim($loct->zip) != '' ? trim($loct->zip) : '---',
            ]);
        }
    }
}
