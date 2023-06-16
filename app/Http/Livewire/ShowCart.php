<?php

namespace App\Http\Livewire;

use App\Models\CartedItem;
use App\Models\Order;
use App\Models\OrderDetail;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ShowCart extends Component implements HasTable
{
    use InteractsWithTable;

    public $shipTo = 0;

    public $shipTos = [];

    public $disabledConfirm = 'disabled';

    public function mount()
    {
        $this->shipTos = request()->user()->locations->pluck('name', 'id')->toArray();
        $this->shipTo = 0;
    }

    public function render()
    {
        return view('livewire.cart');
    }

    protected function getTableQuery(): Builder
    {
        return CartedItem::query()
            ->where('user_id', '=', request()->user()->id);
    }

    public function countCartedItems(): int
    {
        return $this->getTableQuery()->count();
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

    public function submit(): void
    {
        $order = Order::create([
            'user_id' => request()->user()->id,
            'ship_location_id' => $this->shipTo,
            'status' => 'Open',
        ]);

        foreach (CartedItem::whereUserId(request()->user()->id)->get() as $cartedItem) {
            OrderDetail::create([
                'order_id' => $order->id,
                'item_id' => $cartedItem->item->id,
                'quantity_ordered' => $cartedItem->quantity,
            ]);
            $cartedItem->delete();
        }

        $this->redirect(route('order.show', ['id' => $order->id]));
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Your cart is empty';
    }
}
