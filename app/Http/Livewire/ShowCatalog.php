<?php

namespace App\Http\Livewire;

use App\Models\ItemLocation;
use Closure;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
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
            ->where('quantity', '>', 0)
            ->whereRaw('location_id in (select location_id from location_user where user_id = '.request()->user()->id.')');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('location.name')->label('Location')->sortable()->searchable(),
            TextColumn::make('item.group')->label('Group')->sortable()->searchable(),
            TextColumn::make('item.manufacturer')->label('Manufacturer')->sortable()->searchable(),
            TextColumn::make('item.description')->label('Description')->sortable()->searchable(),
            TextColumn::make('item.key')->label('Key')->sortable()->searchable(),
            TextColumn::make('item.supplier_key')->label('Supplier Key')->sortable()->searchable(),
            TextColumn::make('quantity')->sortable(),
        ];
    }

//    protected function paginateTableQuery(Builder $query): Paginator
//    {
//        $q =  $query->simplePaginate($this->getTableRecordsPerPage() == -1 ? $query->count() : $this->getTableRecordsPerPage());
//        if ($this->page === 2) $q->dd();
//        return $q;
//    }

//    protected function getDefaultTableSortColumn(): ?string
//    {
//        return 'item.key';
//    }

//    protected function getTableRecordUrlUsing(): ?Closure
//    {
//        return fn (ItemLocation $record): string => route('product', ['product' => $record]);
//    }
}
