<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use App\Models\BillOfMaterials;
use App\Models\CharterItem;
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
                'key' => $charterItem?->key ??  '---',
                'supplier_key' => $charterItem->supplier_key ?? '---',
                'description' => $charterItem->description ?? $this->getItmdesc($sbtItem),
                'group' => $charterItem->group ?? '---',
                'manufacturer' => $this->getManufacturer($sbtItem),
            ];

            Item::updateOrCreate($itemKey, $itemData);
        }
    }

    private function findCharterItem(string $sbtItem): ?CharterItem
    {
        $itemRegexp = '^' . $sbtItem . '(-.)?$';
        $icsupl = DB::connection('basilisk')
            ->table('icsupl80')
            ->where('vpartno', 'regexp', '^1[0-9]{6}$')
            ->where('vendno', 'like', 'CH/%')
            ->where('item', 'regexp', $itemRegexp)
            ->orderBy('item')
            ->first();

        if ($icsupl){
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

    private function firstOrCreateDetail(mixed $ttsPart, Item $masterItem): BillOfMaterials
    {
        $item = $this->firstOrCreateItem($ttsPart);
        if ($item->id >= 389) dd($item);
        $attributes = [
            'master_item_id' => $masterItem->id,
            'item_id' => $item->id,
        ];
        $values = [
            'min_qty' => 0,
            'max_qty' => 1,
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
}
