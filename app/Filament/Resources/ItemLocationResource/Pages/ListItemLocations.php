<?php

namespace App\Filament\Resources\ItemLocationResource\Pages;

use App\Actions\ImportItemLocations;
use App\Filament\Resources\ItemLocationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItemLocations extends ListRecords
{
    protected static string $resource = ItemLocationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('Import item locations')
                ->action(function (): void {
                    ImportItemLocations::run();
                })
                ->requiresConfirmation(),

            // Actions\CreateAction::make(),
        ];
    }
}
