<?php

namespace App\Http\Livewire;

use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Closure;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ShowCatalog extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.catalog');
    }

    protected function getTableQuery(): Builder
    {
        return ItemLocation::query()
            ->selectRaw('item_locations.id, item_locations.location_id, item_locations.item_id, '.
                'item_locations.quantity, items.`group` as `group`, items.manufacturer as manufacturer')
            ->join('items', 'items.id', '=', 'item_locations.item_id')
            ->join('conditions', 'conditions.id', '=', 'item_locations.condition_id')
            ->where('conditions.show_in_catalog', '<>', 0)
            ->whereIn('location_id', $this->locationIds());
    }

    protected function locationIds(): array
    {
        return DB::table('location_user')
            ->select('location_id')
            ->where('user_id', '=', request()->user()->id)
            ->pluck('location_id')
            ->toArray();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('location.name')->label('Location')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('group')->label('Group')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('manufacturer')->label('Manufacturer')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item.description')->label('Description')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item.key')->label('Key')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item.supplier_key')->label('Supplier Key')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('quantity')->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('exclude_out_of_stock_items')
                ->query(fn (Builder $query): Builder => $query->where('quantity', '>', 0))
                ->default(),
            Tables\Filters\Filter::make('exclude_parts_and_accessories')
                ->query(fn (Builder $query): Builder => $query->whereRaw('item_id not in (select item_id from bill_of_materials)'))
                ->default(),
            Tables\Filters\SelectFilter::make('location')->options($this->locationOptions())->attribute('location_id'),
            Tables\Filters\SelectFilter::make('group')->options($this->groupOptions()),
            Tables\Filters\SelectFilter::make('manufacturer')->options($this->manufacturerOptions()),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (ItemLocation $itemLocation): string => route('item.show', ['id' => $itemLocation->id]);
    }

    private function locationOptions(): array
    {
        return Location::whereIn('id', $this->locationIds())->pluck('name', 'id')->toArray();
    }

    private function groupOptions(): array
    {
        return Item::distinct()
            ->whereIn('id', $this->itemIds())
            ->pluck('group', 'group')
            ->toArray();
    }

    private function manufacturerOptions(): array
    {
        return Item::distinct()
            ->whereIn('id', $this->itemIds())
            ->pluck('manufacturer', 'manufacturer')
            ->toArray();
    }

    private function itemIds(): array
    {
        return DB::table('item_locations')
            ->distinct()
            ->select('item_id')
            ->whereIn('location_id', $this->locationIds())
            ->pluck('item_id')
            ->toArray();
    }
}
