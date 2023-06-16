<?php

namespace App\Http\Livewire\Page\Section;

use App\Models\BillOfMaterials;
use App\Models\CartedItem;
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
        $this->input['bom_items'] = [];
        $this->input['bom_group_prompts'] = [];
        $this->input['bom_groups'] = [];
        $this->input['bom_groups_values'] = [];

        if ($this->itemLocation->item->bill_of_materials->count() > 0) {
            foreach ($this->itemLocation->item->bill_of_materials->toQuery()->selectRaw('distinct option_group')->get() as $group_index => $optionGroup) {
                if ($optionGroup->option_group == null) {
                    foreach ($this->itemLocation->item->bill_of_materials->whereNull('option_group') as $bom) {
                        $this->input['bom_items'][$bom->id]['checked'] = false;
                    }
                } else {
                    $group = $optionGroup->option_group;
                    foreach ($this->itemLocation->item->bill_of_materials->where('option_group', '=', $group) as $bom) {
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
            $this->addItemToCart($this->itemLocation->item->id);
        }

        foreach ($this->input['bom_items'] as $key => $value) {
            if ($value['checked']) {
                $this->addItemToCart(BillOfMaterials::find($key)->item->id);
            }
        }

        foreach ($this->input['bom_groups_values'] as $key => $value) {
            if ($value) {
                $this->addItemToCart(BillOfMaterials::find($value)->item->id);
            }
        }

        $this->redirectRoute('cart.show');
    }

    private function addItemToCart(mixed $id)
    {
        $cartedItem = CartedItem::whereUserId(request()->user()->id)
            ->whereItemId($id)->first();
        if ($cartedItem) {
            $cartedItem->quantity++;
            $cartedItem->save();

            return;
        }
        CartedItem::create([
            'user_id' => request()->user()->id,
            'item_id' => $id,
            'quantity' => 1,
        ]);
    }
}
