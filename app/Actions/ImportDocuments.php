<?php

namespace App\Actions;

use App\Models\Document;
use App\Models\Item;
use FilesystemIterator;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use SplFileInfo;

class ImportDocuments
{
    use AsAction;

    public string $commandSignature = 'import:documents';

    public string $commandDescription = 'Import documents from the old AT&T TTS Program';

    public function asCommand(Command $command): void
    {
        $this->handle();
    }

    public function handle(): void
    {
        $docPath = storage_path().'/app/import/doc';
        $i = new FilesystemIterator($docPath, FilesystemIterator::SKIP_DOTS);
        foreach ($i as $fileInfo) {
            $this->importDir($fileInfo);
        }
    }

    private function importDir(SplFileInfo $fileInfo): void
    {
        if (! $fileInfo->isDir()) {
            return;
        }
        $sbt_item = $fileInfo->getFilename();
        $item = Item::whereSbtItem($sbt_item)->first();
        if (! $item) {
            return;
        }
        $i = new FilesystemIterator($fileInfo->getPathname(), FilesystemIterator::SKIP_DOTS);
        foreach ($i as $fileInfo) {
            $this->importDocFile($fileInfo, $item);
        }
    }

    private function importDocFile(SplFileInfo $fileInfo, Item $item): void
    {
        if (! $fileInfo->isFile()) {
            return;
        }
        $sourcePath = $fileInfo->getPathname();
        $destinationDir = storage_path().'/app/public/documents/'.$item->sbt_item;
        $destinationPath = storage_path().'/app/public/documents/'.$item->sbt_item.'/'.$fileInfo->getFilename();
        if (! file_exists($destinationDir)) {
            mkdir($destinationDir);
        }
        if (! copy($sourcePath, $destinationPath)) {
            return;
        }
        $hash = hash_file('sha256', $destinationPath);
        Document::firstOrCreate([
            'item_id' => $item->id,
            'hash' => $hash,
        ], [
            'title' => $fileInfo->getFilename(),
            'path' => 'documents/'.$item->sbt_item.'/'.$fileInfo->getFilename(),
        ]);
    }
}
