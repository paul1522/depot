<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Actions\ImportLocations;
use App\Filament\Resources\LocationResource;
use Exception;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    /**
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            Actions\Action::make('Import')
                ->action(function (): void {
                    $this->importIcloct80();
                })
                ->requiresConfirmation(),
            // Actions\CreateAction::make(),
        ];
    }

    private function importIcloct80(): void
    {
        ImportLocations::run();
    }
}
