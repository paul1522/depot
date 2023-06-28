<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Actions\ImportBom;
use App\Actions\ImportDocuments;
use App\Actions\ImportImages;
use App\Actions\ImportItems;
use App\Filament\Resources\ItemResource;
use Exception;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    /**
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('Import')
                    ->action(function (): void {
                        ImportItems::run();
                    })
                    ->requiresConfirmation(),
                Actions\Action::make('Import BOM')->label('Import BOM')
                    ->action(function (): void {
                        ImportBom::run();
                    })
                    ->requiresConfirmation(),
                Actions\Action::make('Import images')
                    ->action(function (): void {
                        ImportImages::run();
                    })
                    ->requiresConfirmation(),
                Actions\Action::make('Import documents')
                    ->action(function (): void {
                        ImportDocuments::run();
                    })
                    ->requiresConfirmation(),
            ])->label('Import'),
            // Actions\CreateAction::make(),
        ];
    }
}
