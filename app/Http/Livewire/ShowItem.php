<?php

namespace App\Http\Livewire;

use App\Models\ItemLocation;
use Filament\Forms;
use Livewire\Component;

class ShowItem extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    private ItemLocation $itemLocation;

    public function mount($id): void
    {
        $this->itemLocation = ItemLocation::find($id);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.item', [
            'itemLocation' => $this->itemLocation,
        ]);
    }
}
