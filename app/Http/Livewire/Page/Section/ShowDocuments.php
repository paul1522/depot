<?php

namespace App\Http\Livewire\Page\Section;

use App\Models\ItemLocation;
use Livewire\Component;

class ShowDocuments extends Component
{
    public ItemLocation $itemLocation;

    public function render()
    {
        return view('livewire.page.section.documents');
    }
}
