<?php

namespace App\Http\Livewire;

use Livewire\Component;

class InventoryTransationsReport extends Component
{
    public function render()
    {
        return view('livewire.inventory-transations-report')
            ->layout('layouts.app', [
                'drawer_open' => true,
            ]);
    }
}
