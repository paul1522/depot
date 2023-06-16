<?php

namespace App\Filament\Resources\CharterItemResource\Pages;

use App\Actions\ImportCharterItems;
use App\Filament\Resources\CharterItemResource;
use Exception;
use Filament\Forms;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListCharterItems extends ListRecords
{
    protected static string $resource = CharterItemResource::class;

    /**
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            Actions\Action::make('Import')
                ->action(function (array $data): void {
                    $this->importCsv($data['filename']);
                })
                ->form([
                    Forms\Components\FileUpload::make('filename')
                        ->disk('local')
                        ->directory('private')
                        ->required()
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel']),
                ]),
            Actions\CreateAction::make(),
        ];
    }

    private function importCsv(mixed $filename): void
    {
        $file = Storage::disk('local')->path($filename);
        ImportCharterItems::run($file);
        unlink($file);
    }
}
