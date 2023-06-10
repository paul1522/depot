<?php

namespace App\Http\Livewire;

use App\Models\CartedItem;
use App\Models\ItemLocation;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Filament\Tables;

class ShowCart extends Component implements HasTable
{
    use InteractsWithTable;
    public function render()
    {
        return view('livewire.cart');
    }

    protected function getTableQuery(): Builder
    {
        return CartedItem::query()
            ->where('user_id', '=', request()->user()->id);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('item.description')->label('Description')->sortable(),
            Tables\Columns\TextColumn::make('quantity')->label('Qty')->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\DeleteAction::make('delete'),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\DeleteBulkAction::make(),
        ];
    }

}
