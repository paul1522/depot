<?php

namespace App\Http\Livewire;

use App\Models\ItemLocation;
use Closure;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
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
        return ItemLocation::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('part_key')->label('Part')->sortable()->searchable(),
            TextColumn::make('manufacturer')->sortable()->searchable(),
            TextColumn::make('description')->sortable()->searchable(),
            TextColumn::make('location')->sortable()->searchable(),
            TextColumn::make('qty_on_hand'),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'part_key';
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (ItemLocation $record): string => route('product', ['product' => $record]);
    }
}
