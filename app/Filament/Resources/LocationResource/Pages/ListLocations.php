<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use App\Models\Location;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('Import from icloct80')
                ->action(function (): void {
                    $this->importIciloc80();
                })
                ->requiresConfirmation(),

            Actions\CreateAction::make(),
        ];
    }

    private function importIciloc80()
    {
        $icloct = DB::connection('basilisk')
            ->table('icloct80')
            ->where('loctid', 'like', 'CH/%')
            ->get();

        foreach ($icloct as $loct) {
            Location::updateOrCreate([
                'name' => $loct->locdesc,
            ], [
                'address1' => $loct->addrs1,
                'address2' => $loct->addrs2,
                'city' => $loct->city,
                'state' => $loct->state,
                'zip' => $loct->zip,
                'sbt_loctid' => $loct->loctid,
            ]);
        }
    }
}
