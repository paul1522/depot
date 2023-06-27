<?php

namespace App\Actions;

use App\Models\CharterItem;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportItems
{
    use AsAction;

    public function handle(): void
    {
        $iciloc = DB::connection('basilisk')
            ->table('iciloc80')
            ->where('loctid', 'like', 'CH/%')
            ->get();

        foreach ($iciloc as $iloc) {
            $sbtItem = mb_ereg_replace('-[A-Z]$', '', trim($iloc->item));
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
}