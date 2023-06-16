<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Closure;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ShowOrders extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.orders');
    }

    protected function getTableQuery(): Builder
    {
        return Order::query()->where('user_id', '=', request()->user()->id);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->label('Order#')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('created_at')->label('Date')->sortable()->searchable()->date(),
            Tables\Columns\TextColumn::make('ship_location.name')->label('Ship To')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('status')->label('Status')->sortable()->searchable(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Order $record) {
            return route('order.show', ['id' => $record->id]);
        };
    }
}
