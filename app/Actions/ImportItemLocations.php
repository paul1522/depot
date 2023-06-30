<?php

namespace App\Actions;

use App\Models\Condition;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportItemLocations
{
    use AsAction;

    public function handle(): void
    {
        $iciloc = DB::connection('basilisk')
            ->table('iciloc80')
            ->join('icitem80', 'icitem80.item', '=', 'iciloc80.item')
            ->where('loctid', 'like', 'CH/%')
            ->where('icitem80.type', '=', 'I')
            ->get();

        ItemLocation::query()->update(['quantity' => 0]);

        foreach ($iciloc as $iloc) {
            $matches = [];
            $conditionCode = null;
            if (mb_ereg('-([A-Z])', $iloc->item, $matches)) {
                if (count($matches) === 2) {
                    $conditionCode = $matches[1];
                }
            }

            $sbtItem = mb_ereg_replace('-.$', '', $iloc->item);

            $itemKey = [
                'item_id' => $this->getItemId($sbtItem),
                'location_id' => $this->getLocationId($iloc->loctid),
                'condition_id' => $this->getConditionId($conditionCode),
            ];
            $itemData = [
                'quantity' => 0,
            ];

            $itemLocation = ItemLocation::firstOrCreate($itemKey, $itemData);
            $itemLocation->quantity += $iloc->lonhand;
            $itemLocation->save();
        }
    }

    private function getItemId(string $sbtItem): int
    {
        return Item::where('sbt_item', '=', $sbtItem)->firstOrFail()->id;
    }

    private function getLocationId(string $sbtLoctid): int
    {
        return Location::where('sbt_loctid', '=', $sbtLoctid)->firstOrFail()->id;
    }

    private function getConditionId(?string $conditionCode): ?int
    {
        if (! $conditionCode) {
            $condition = Condition::whereNull('sbt_suffix')->first();
        } else {
            $condition = Condition::where('sbt_suffix', '=', $conditionCode)->first();
        }
        if ($condition) {
            return $condition->id;
        }
        $condition = Condition::create([
            'sbt_suffix' => ! $conditionCode ? null : $conditionCode,
            'name' => "-{$conditionCode}-",
        ]);

        return $condition->id;
    }
}
