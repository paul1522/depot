<?php

namespace App\Http\Livewire;

use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Closure;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
            ->selectRaw('min(item_locations.id) as id, item_locations.item_id, item_locations.location_id,
            sum(item_locations.quantity) as sum_quantity')
            ->join('items', 'item_locations.item_id', '=', 'items.id')
            ->join('locations', 'locations.id', '=', 'item_locations.location_id')
            ->join('location_user', 'location_user.location_id', '=', 'locations.id')
            ->join('conditions', 'conditions.id', '=', 'item_locations.condition_id')
            ->where('conditions.show_in_catalog', '=', 1)
            ->where('location_user.user_id', '=', request()->user()->id)
            ->groupByRaw('item_locations.item_id, item_locations.location_id');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('location.name')->label('Location')->sortable(),
            Tables\Columns\TextColumn::make('item.group')->label('Group')->sortable(),
            Tables\Columns\TextColumn::make('item.manufacturer')->label('Manufacturer')->sortable(),
            Tables\Columns\TextColumn::make('item.description')->label('Description')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item.key')->label('Key')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item.supplier_key')->label('Supplier Key')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('sum_quantity')->label('Quantity')->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('exclude_out_of_stock_items')
                ->query(fn (Builder $query): Builder => $query->whereRaw('(item_locations.quantity > 0)'))
                ->default(),
            Tables\Filters\Filter::make('exclude_parts_and_accessories')
                ->query(fn (Builder $query): Builder => $query->whereRaw('(item_locations.item_id not in (select item_id from bill_of_materials))'))
                ->default(),
            Tables\Filters\SelectFilter::make('location')
                ->options($this->locationOptions())
                ->attribute('location.id'),
            Tables\Filters\Filter::make('group')
                ->form([
                    Forms\Components\Select::make('group')->options($this->groupOptions())->placeholder('All'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if (!$data['group']) return $query;
                    return $query->whereRaw('`item_id` in (select id from items where `group` = \''. $data['group'] .'\')');
                }),
            Tables\Filters\Filter::make('manufacturer')
                ->form([
                    Forms\Components\Select::make('manufacturer')->options($this->manufacturerOptions())->placeholder('All'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if (!$data['manufacturer']) return $query;
                    return $query->whereRaw('`item_id` in (select id from items where `manufacturer` = \''. $data['manufacturer'] .'\')');
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (ItemLocation $itemLocation): string => route('item.show', [
            'item' => $itemLocation->item_id,
            'location' => $itemLocation->location_id
        ]);
    }

    private function locationOptions(): array
    {
        return Location::whereIn('id', $this->locationIds())->orderBy('name')->pluck('name', 'id')->toArray();
    }

    private function groupOptions(): array
    {
        return Item::distinct()
            ->pluck('group', 'group')
            ->toArray();
    }

    private function manufacturerOptions(): array
    {
        return Item::distinct()
            ->pluck('manufacturer', 'manufacturer')
            ->toArray();
    }

    protected function locationIds(): array
    {
        return DB::table('location_user')
            ->select('location_id')
            ->where('user_id', '=', request()->user()->id)
            ->pluck('location_id')
            ->toArray();
    }
}
