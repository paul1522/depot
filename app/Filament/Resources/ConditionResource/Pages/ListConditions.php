<?php

namespace App\Filament\Resources\ConditionResource\Pages;

use App\Filament\Resources\ConditionResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConditions extends ListRecords
{
    protected static string $resource = ConditionResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
