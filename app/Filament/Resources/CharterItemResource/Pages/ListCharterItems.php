<?php

namespace App\Filament\Resources\CharterItemResource\Pages;

use App\Filament\Resources\CharterItemResource;
use App\Models\CharterItem;
use Filament\Forms;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListCharterItems extends ListRecords
{
    protected static string $resource = CharterItemResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('Import from file')
                ->action(function (array $data): void {
                    $this->importCsv($data['filename']);
                })
                ->form([
                    Forms\Components\FileUpload::make('filename')
                        ->disk('local')
                        ->directory('private')
                        ->required()
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel'])
                ]),
            Actions\CreateAction::make(),
        ];
    }

    private function importCsv(mixed $filename)
    {
        $file = Storage::disk('local')->path($filename);
        $csvFile = fopen($file, 'r');

        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstLine) {
                CharterItem::updateOrCreate([
                    'key' => $data['0']
                ], [
                    'supplier_key' => $data['1'],
                    'description' => $data['2'],
                    'group' => $data['3'],
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
        unlink($file);
    }
}
