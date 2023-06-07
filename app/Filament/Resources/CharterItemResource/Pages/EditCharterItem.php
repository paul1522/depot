<?php

namespace App\Filament\Resources\CharterItemResource\Pages;

use App\Filament\Resources\CharterItemResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCharterItem extends EditRecord
{
    protected static string $resource = CharterItemResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
