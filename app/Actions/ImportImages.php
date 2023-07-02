<?php

namespace App\Actions;

use App\Models\Item;
use FilesystemIterator;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use SplFileInfo;

class ImportImages
{
    use AsAction;

    public string $commandSignature = 'import:images';

    public string $commandDescription = 'Import product images from the old AT&T TTS Program';

    public function asCommand(Command $command): void
    {
        $this->handle();
    }

    public function handle(): void
    {
        $imgPath = storage_path().'/app/import/image';
        $i = new FilesystemIterator($imgPath, FilesystemIterator::SKIP_DOTS);
        foreach ($i as $fileInfo) {
            $this->importFile($fileInfo);
        }
    }

    private function importFile(SplFileInfo $fileInfo): void
    {
        if (! $fileInfo->isFile()) {
            return;
        }
        $mimeType = mime_content_type($fileInfo->getPathname());
        if ('image/' !== mb_substr($mimeType, 0, 6)) {
            return;
        }
        $this->importImage($fileInfo);
    }

    private function importImage(SplFileInfo $fileInfo): void
    {
        $sourcePath = $fileInfo->getPathname();
        $destinationPath = storage_path().'/app/public/images/'.$fileInfo->getFilename();
        $sbt_item = mb_ereg_replace('\.[a-z]+$', '', $fileInfo->getFilename());
        $item = Item::whereSbtItem($sbt_item)->first();
        if (! $item) {
            return;
        }
        if ($item->image_path) {
            return;
        }
        if (! copy($sourcePath, $destinationPath)) {
            return;
        }
        $item->image_path = 'images/'.$fileInfo->getFilename();
        $item->image_name = $fileInfo->getFilename();
        $item->save();
    }
}
