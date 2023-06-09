<?php

namespace App\Http\Livewire;

use App\Models\Item;
use App\Models\ItemLocation;
use Closure;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ShowCatalog extends Component implements HasTable
{
    use InteractsWithTable;

    public function render()
    {
        return view('livewire.catalog');
    }

    protected function getTableQuery(): Builder
    {
        /*
         * select *
         * from `item_locations`
         * inner join `location_user` on `location_user`.`location_id` = `item_locations`.`location_id`
         * where `quantity` > ?
         * and `location_user`.`user_id` = ?
         */
        return ItemLocation::query()
            // ->where('quantity', '>', 0)
            ->whereRaw('location_id in (select location_id from location_user where user_id = '.request()->user()->id.')');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('location.name')->label('Location')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item.group')->label('Group')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item.manufacturer')->label('Manufacturer')->sortable()->searchable(),
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
            Tables\Filters\SelectFilter::make('location')
                ->relationship('location', 'name'),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (ItemLocation $itemLocation): string => route('item.show', ['id' => $itemLocation->id]);
    }
}
