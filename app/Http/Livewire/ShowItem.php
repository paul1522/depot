<?php

namespace App\Http\Livewire;

use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Filament\Forms;
use Livewire\Component;

class ShowItem extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;


    private Item $item;
    private Location $location;

    public function mount($item, $location): void
    {
        $this->item = Item::find($item);
        $this->location = Location::find($location);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.item', [
            'item' => $this->item,
            'location' => $this->location,
        ])
            ->layout('layouts.app', [
                'drawer_open' => false
            ]);
    }
}
