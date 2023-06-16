<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use App\Models\BillOfMaterials;
use App\Models\CharterItem;
use App\Models\Document;
use App\Models\Item;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('Import images from TTS')->label('Import images from TTS')
                ->action(function (): void {
                    $this->importImages();
                })
                ->requiresConfirmation(),
            Actions\Action::make('Import documents from TTS')->label('Import documents from TTS')
                ->action(function (): void {
                    $this->importDocuments();
                })
                ->requiresConfirmation(),
            Actions\Action::make('Import BOM from TTS')->label('Import BOM from TTS')
                ->action(function (): void {
                    $this->importBom();
                })
                ->requiresConfirmation(),
            Actions\Action::make('Import from iciloc80')
                ->action(function (): void {
                    $this->importIciloc80();
                })
                ->requiresConfirmation(),
            Actions\CreateAction::make(),
        ];
    }

    private function importIciloc80(): void
    {
        $iciloc = DB::connection('basilisk')
            ->table('iciloc80')
            ->where('loctid', 'like', 'CH/%')
            ->get();

        foreach ($iciloc as $iloc) {
            $sbtItem = mb_ereg_replace('-.$', '', trim($iloc->item));
            $charterItem = $this->findCharterItem($sbtItem);

            $itemKey = [
                'sbt_item' => $sbtItem,
            ];
            $itemData = [
                'key' => $charterItem?->key ?? '---',
                'supplier_key' => $charterItem->supplier_key ?? '---',
                'description' => $charterItem->description ?? $this->getItmdesc($sbtItem),
                'group' => $charterItem->group ?? '---',
                'manufacturer' => $this->getManufacturer($sbtItem),
            ];

            Item::firstOrCreate($itemKey, $itemData);
        }
    }

    private function findCharterItem(string $sbtItem): ?CharterItem
    {
        $itemRegexp = '^'.$sbtItem.'(-.)?$';
        $icsupl = DB::connection('basilisk')
            ->table('icsupl80')
            ->where('vpartno', 'regexp', '^1[0-9]{6}$')
            ->where('vendno', 'like', 'CH/%')
            ->where('item', 'regexp', $itemRegexp)
            ->orderBy('item')
            ->first();

        if ($icsupl) {
            return CharterItem::where('key', '=', $icsupl->vpartno)->first();
        }

        return null;
    }

    private function getItmdesc(string $sbtItem): string
    {
        $icitem = DB::connection('basilisk')
            ->table('icitem80')
            ->where('item', '=', $sbtItem)
            ->first();

        return $icitem?->itmdesc ?? '---';
    }

    private function getManufacturer(string $sbtItem): string
    {
        $icmanu = DB::connection('basilisk')
            ->table('icmanu66')
            ->join('icitem80', 'icitem80.code', '=', 'icmanu66.code')
            ->where('item', '=', $sbtItem)
            ->first();

        return mb_ereg_replace(';[A-Z]+', '', $icmanu?->name ?? '---');
    }

    private function importBom(): void
    {
        foreach (Item::all() as $item) {
            $this->importTtsParts(
                DB::connection('gluttony_2')
                    ->table('web_ttsparts')
                    ->where(DB::raw('regexp_replace(web_ttsparts.master_item, \'-.$\', \'\')'), '=', $item->sbt_item)
                    ->get(),
                $item
            );
        }
    }

    private function importTtsParts(Collection $ttsParts, Item $item): void
    {
        foreach ($ttsParts as $ttsPart) {
            $this->importTtsPart($ttsPart, $item);
        }
    }

    private function importTtsPart(mixed $ttsPart, Item $item): void
    {
        $this->firstOrCreateDetail($ttsPart, $item);
    }

    private function firstOrCreateDetail(mixed $ttsPart, Item $master_item): BillOfMaterials
    {
        $item = $this->firstOrCreateItem($ttsPart);
        $attributes = [
            'master_item_id' => $master_item->id,
            'item_id' => $item->id,
        ];
        $values = [
        ];

        return BillOfMaterials::firstOrCreate($attributes, $values);
    }

    private function firstOrCreateItem(mixed $ttsPart): Item
    {
        $sbtItem = mb_ereg_replace('-.$', '', trim($ttsPart->item));
        $attributes = [
            'sbt_item' => $sbtItem,
        ];
        $values = [
            'description' => $ttsPart->description,
            'group' => '----',
            'key' => '----',
            'manufacturer' => '----',
            'supplier_key' => '----',
        ];

        return Item::firstOrCreate($attributes, $values);
    }

    private function importImages(): void
    {
        $imgPath = storage_path().'/app/import/image';
        $i = new \FilesystemIterator($imgPath, \FilesystemIterator::SKIP_DOTS);
        foreach ($i as $fileInfo) {
            $this->importFile($fileInfo);
        }
    }

    public function importFile(\SplFileInfo $fileInfo): void
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

    private function importImage(\SplFileInfo $fileInfo): void
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

    private function importDocuments(): void
    {
        $docPath = storage_path().'/app/import/doc';
        $i = new \FilesystemIterator($docPath, \FilesystemIterator::SKIP_DOTS);
        foreach ($i as $fileInfo) {
            $this->importDir($fileInfo);
        }
    }

    private function importDir(\SplFileInfo $fileInfo): void
    {
        if (! $fileInfo->isDir()) {
            return;
        }
        $sbt_item = $fileInfo->getFilename();
        $item = Item::whereSbtItem($sbt_item)->first();
        if (! $item) {
            return;
        }
        $i = new \FilesystemIterator($fileInfo->getPathname(), \FilesystemIterator::SKIP_DOTS);
        foreach ($i as $fileInfo) {
            $this->importDocFile($fileInfo, $item);
        }
    }

    private function importDocFile(\SplFileInfo $fileInfo, Item $item)
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
