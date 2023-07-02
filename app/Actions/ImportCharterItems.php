<?php

namespace App\Actions;

use App\Models\CharterItem;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportCharterItems
{
    use AsAction;

    public string $commandSignature = 'import:charter-items {filename}';

    public string $commandDescription = 'Import Charter items from a csv file.';

    public function asCommand(Command $command): void
    {
        $filename = $command->argument('filename');
        $this->handle($filename);
    }

    public function handle(string $file): void
    {
        $csvFile = fopen($file, 'r');

        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000)) !== false) {
            if (! $firstLine) {
                CharterItem::firstOrCreate([
                    'key' => $data['0'],
                ], [
                    'supplier_key' => $data['1'],
                    'description' => $data['2'],
                    'group' => $data['3'],
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }
}
