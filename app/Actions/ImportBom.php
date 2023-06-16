<?php

namespace App\Actions;

use App\Models\BillOfMaterials;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportBom
{
    use AsAction;

    public function handle()
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
            'group' => '---',
            'key' => '---',
            'manufacturer' => '---',
            'supplier_key' => '---',
        ];

        return Item::firstOrCreate($attributes, $values);
    }
}
