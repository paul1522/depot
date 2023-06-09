<?php

namespace App\Filament\Resources\ItemLocationResource\Pages;

use App\Filament\Resources\ItemLocationResource;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListItemLocations extends ListRecords
{
    protected static string $resource = ItemLocationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('Import from iciloc80')
                ->action(function (): void {
                    $this->importIciloc80();
                })
                ->requiresConfirmation(),
            Actions\CreateAction::make(),
        ];
    }

    private function importIciloc80(): void
    {
        $iciloc = DB::connection('basilisk')
            ->table('iciloc80')
            ->where('loctid', 'like', 'CH/%')
            ->get();

        ItemLocation::query()->update(['quantity' => 0]);

        foreach ($iciloc as $iloc) {
            $sbtItem = mb_ereg_replace('-.$', '', $iloc->item);

            $itemKey = [
                'item_id' => $this->getItemId($sbtItem),
                'location_id' => $this->getLocationId($iloc->loctid),
            ];
            $itemData = [
                'quantity' => 0,
            ];

            $itemLocation = ItemLocation::firstOrCreate($itemKey, $itemData);
            $itemLocation->quantity += $iloc->lonhand;
            $itemLocation->save();
        }
    }

    private function getItemId(string $sbtItem): string
    {
        return Item::where('sbt_item', '=', $sbtItem)->firstOrFail()->id;
    }

    private function getLocationId(string $sbtLoctid): string
    {
        return Location::where('sbt_loctid', '=', $sbtLoctid)->firstOrFail()->id;
    }
}
