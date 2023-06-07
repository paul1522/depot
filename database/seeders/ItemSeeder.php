<?php

namespace Database\Seeders;

use App\Models\CharterItem;
use App\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    private ?string $description;
    private ?string $key;
    private ?CharterItem $charterItem;

    public function run(): void
    {
        Item::truncate();
        foreach ($this->getSbtItems() as $sbtItem) {
            $this->createItem($sbtItem);
        }
    }

    public function getSbtItems(): Collection
    {
        /*
         * select distinct regexp_replace(item, '-.$', '') as item
         * from iciloc80
         * where loctid like 'CH/%'
         * and lonhand > 0
         */
        return DB::connection('basilisk')
            ->table('iciloc80')
            ->select([
                DB::raw('regexp_replace(iciloc80.item, \'-.$\', \'\') as sbt_item'),
                'icmanu66.name as manufacturer',
                'icitem80.itmdesc',
            ])
            ->distinct()
            ->leftJoin('icitem80', 'icitem80.item', '=', 'iciloc80.item')
            ->leftJoin('icmanu66', 'icmanu66.code', '=', 'icitem80.code')
            ->where('iciloc80.loctid', 'like', 'CH/%')
            ->where('iciloc80.lonhand', '>', '0')
            ->get();
    }

    public function createItem($sbtItem): void
    {
        $manufacturer = mb_ereg_replace(';[A-Z]+', '',   $sbtItem->manufacturer);
        $itmdesc = $sbtItem->itmdesc;
        $sbtItem = $sbtItem->sbt_item;
        $charterItem = $this->getCharterItem($sbtItem);

        Item::create([
            'key' => $charterItem?->key ?? '---',
            'supplier_key' => $charterItem?->supplier_key ?? '---',
            'description' => $charterItem?->description ?? $itmdesc ?? '---',
            'group' => $charterItem?->group ?? '---',
            'manufacturer' => $manufacturer,
            'sbt_item' => $sbtItem,
        ]);
    }

    private function getCharterItem(string $sbtItem): ?CharterItem
    {
        $vpartnos = DB::connection('basilisk')
            ->table('icsupl80')->distinct()->select('vpartno')
            ->where('vendno', 'like', 'CH/%')
            ->where('vpartno', 'like', '1______')
            ->where(function ($query) use ($sbtItem) {
                $query->where('item', '=', $sbtItem)
                    ->orWhere('item', 'like', $sbtItem . '-_');
            })
            ->pluck('vpartno');
        if ($vpartnos->count() === 0) return null;
        // dump($vpartnos->first());
        return CharterItem::where('key', '=', $vpartnos->first())->first();
    }

    public function getDescription(string $sbtItem): string
    {
        $icitem = DB::connection('basilisk')
            ->table('icitem80')
            ->where('item', '=', $sbtItem)
            ->get()
            ->first();
        return $icitem?->itmdesc ?? '';
    }

    public function getKey(string $sbtItem): ?string
    {
        $icsupl = DB::connection('basilisk')
            ->table('icsupl80')
            ->where('item', '=', $sbtItem)
            ->get()
            ->first();
        return $icsupl?->vpartno;
    }

    public function getGroup(): ?string
    {
        $group = '';
        if ($this->key) {
            $this->charterItem = CharterItem::whereKey($this->key)->first();
            $this->description = $this->charterItem?->description;
            $group = $this->charterItem?->group;
        }
        return $group;
    }
}
