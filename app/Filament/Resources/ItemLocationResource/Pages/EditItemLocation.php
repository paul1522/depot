<?php

namespace App\Filament\Resources\ItemLocationResource\Pages;

use App\Filament\Resources\ItemLocationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditItemLocation extends EditRecord
{
    protected static string $resource = ItemLocationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
