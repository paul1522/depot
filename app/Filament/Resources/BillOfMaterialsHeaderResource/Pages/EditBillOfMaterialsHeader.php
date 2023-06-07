<?php

namespace App\Filament\Resources\BillOfMaterialsHeaderResource\Pages;

use App\Filament\Resources\BillOfMaterialsHeaderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBillOfMaterialsHeader extends EditRecord
{
    protected static string $resource = BillOfMaterialsHeaderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
