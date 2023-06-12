<?php

namespace App\Http\Livewire;

use App\Models\Location;
use App\Models\Order;
use App\Models\OrderDetail;
use Filament\Tables;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ShowOrder extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;

    public Order $order;
    public Location $ship_to;

    public function mount($id)
    {
        $this->order = Order::find($id);
        $this->ship_to = Location::find($this->order->ship_location_id);
    }

    public function render()
    {
        return view('livewire.order');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Fieldset::make('Depot order')->inlineLabel()->columns(1)->columnSpan(1)->schema([
                    Forms\Components\TextInput::make('order.id')->label('Order #')->extraInputAttributes(['readonly' => 'readonly']),
                    Forms\Components\DatePicker::make('order.created_at')->label('Date')->disabled(),
                ]),
                Forms\Components\Fieldset::make('Ship to')->inlineLabel()->columns(1)->columnSpan(1)->schema([
                    Forms\Components\TextInput::make('ship_to.name')->extraInputAttributes(['readonly' => 'readonly']),
                    Forms\Components\TextInput::make('ship_to.address1')->extraInputAttributes(['readonly' => 'readonly']),
                    Forms\Components\TextInput::make('ship_to.address2')->extraInputAttributes(['readonly' => 'readonly']),
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\TextInput::make('ship_to.city')->extraInputAttributes(['readonly' => 'readonly'])->columnSpan(2),
                        Forms\Components\TextInput::make('ship_to.state')->extraInputAttributes(['readonly' => 'readonly']),
                        Forms\Components\TextInput::make('ship_to.zip')->extraInputAttributes(['readonly' => 'readonly']),
                    ]),
                ]),
            ]),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return OrderDetail::query()->where('order_id', '=', $this->order->id);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('item.description')->label('Description'),
            Tables\Columns\TextColumn::make('quantity_ordered')->label('Qty Ordered'),
            Tables\Columns\TextColumn::make('quantity_shipped')->label('Qty Shipped'),
        ];
    }

}
