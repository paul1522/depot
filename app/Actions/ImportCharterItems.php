<?php

namespace App\Actions;

use App\Models\CharterItem;
use App\Models\Item;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportCharterItems
{
    use AsAction;

    public string $commandSignature = 'import:charter-items
    {filename : Path to the csv file.}
    {--brutal : Also update the items table records with a matching key. This is useful when Charter send us a new list.}';

    public string $commandDescription = 'Import Charter items from a csv file.';

    public function asCommand(Command $command): void
    {
        $this->handle(
            $command->argument('filename'),
            $command->option('brutal')
        );
    }

    public function handle(string $file, bool $brutal): void
    {
        $csvFile = fopen($file, 'r');

        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000)) !== false) {
            if (! $firstLine) {
                $charterItem = CharterItem::firstOrCreate([
                    'key' => $data['0'],
                ], [
                    'supplier_key' => $data['1'],
                    'description' => $data['2'],
                    'group' => $data['3'],
                ]);
                if ($brutal) {
                    $this->updateItems($charterItem);
                }
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }

    private function updateItems(CharterItem $charterItem): void
    {
        Item::where('key', '=', $charterItem->key)
            ->update([
                'supplier_key' => $charterItem->supplier_key,
                'description' => $charterItem->description,
                'group' => $charterItem->group,
            ]);
    }
}
