<?php

namespace App\Filament\Resources\ConditionResource\Pages;

use App\Filament\Resources\ConditionResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCondition extends EditRecord
{
    protected static string $resource = ConditionResource::class;

    protected function getActions(): array
    {
        return [
            // DeleteAction::make(),
        ];
    }
}
