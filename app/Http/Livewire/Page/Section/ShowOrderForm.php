<?php

namespace App\Http\Livewire\Page\Section;

use App\Models\ItemLocation;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ShowOrderForm extends Component
{
    public ItemLocation $itemLocation;

    public $input = [];
    public $backUrl;
    public $action;

    public function mount(): void
    {
        $this->backUrl = url()->previous();
        $this->input = [];
        $this->input['master']['checked'] = true;
        $this->input['bom_group_prompts'] = [];
        $this->input['bom_items'] = [];
        $this->input['bom_groups'] = [];

        if ($this->itemLocation->item->billOfMaterials->count() > 0) {
            foreach ($this->itemLocation->item->billOfMaterials->toQuery()->selectRaw('distinct option_group')->get() as $group_index => $optionGroup) {
                if ($optionGroup->option_group == null) {
                    foreach ($this->itemLocation->item->billOfMaterials->whereNull('option_group') as $bom) {
                        $this->input['bom_items'][$bom->id]['checked'] = false;
                    }
                } else {
                    $group = $optionGroup->option_group;
                    foreach ($this->itemLocation->item->billOfMaterials->where('option_group', '=', $group) as $bom) {
                        $this->input['bom_group_prompts'][$group_index] = $group;
                        $this->input['bom_groups'][$group_index][$bom->id]['checked'] = false;
                    }
                }
            }
        }
    }

    public function render(): View
    {
        return view('livewire.page.section.order-form');
    }

    public function cancel(): void
    {
        $this->redirect($this->backUrl);
    }

    public function submit(): void
    {
        if ($this->input['master']['checked']) {
            // add the master item to the cart
        }

        foreach ($this->input['bom_items'] as $key => $value) {
            if ($value['checked']) {
                // add the bom item to the cart
            }
        }

        foreach ($this->input['bom_group_values'] as $key => $value) {
            if ($value) {
                // add the bom group item to the card
            }
        }

        $this->redirectRoute('cart.show');
    }
}
