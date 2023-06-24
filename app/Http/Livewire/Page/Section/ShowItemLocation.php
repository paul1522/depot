<?php

namespace App\Http\Livewire\Page\Section;

use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Livewire\Component;

class ShowItemLocation extends Component
{
    public Item $item;
    public Location $location;

    public function render()
    {
        return view('livewire.page.section.item-location');
    }
}
