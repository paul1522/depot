<?php

namespace App\Http\Livewire;

use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Livewire\Component;

class OrderStatusReport extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.order-status-report');
    }

    protected function getTableQuery(): Builder
    {
        return OrderDetail::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('order.id')->label('Order #')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('order.created_at')->label('Date')->sortable()->searchable()->date(),
            Tables\Columns\TextColumn::make('order.ship_location.name')->label('Ship to')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item.description')->label('Description')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('quantity_ordered')->label('Qty ordered')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('quantity_shipped')->label('Qty shipped')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('order.status')->label('Status')->sortable()->searchable(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No orders found';
    }
}
