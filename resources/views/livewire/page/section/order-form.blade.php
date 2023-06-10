<div class="">
    <form wire:submit.prevent="submit">
        <p class="my-2 text-black dark:text-white">Select the items and options to include in this requisition.</p>

        <div>
            <div class="inline-block w-8"></div>
            <div class="inline-block w-8"><input type="checkbox" wire:model="input.master.checked" class="dui-checkbox" /></div>
            <div class="inline-block">{{ $itemLocation->item->description }}</div>
        </div>

        @foreach($input['bom_items'] as $key => $bom)
            <div>
                <div class="inline-block w-8"></div>
                <div class="inline-block w-8"><input type="checkbox" wire:model="input.bom_items.{{ $key }}.checked" class="dui-checkbox" /></div>
                <div class="inline-block">{{ \App\Models\BillOfMaterials::find($key)->item->description }}</div>
            </div>
        @endforeach

        @foreach($input['bom_group_prompts'] as $group_index => $prompt)
            <div>{{ $prompt }}</div>
            @foreach($input['bom_groups'][$group_index] as $key => $bom)
                <div>
                    <div class="inline-block w-8"></div>
                    <div class="inline-block w-8"><input type="radio" wire:model="input.bom_groups_values.{{ $prompt }}" value="{{ $key }}" class="dui-radio" /></div>
                    <div class="inline-block">{{ \App\Models\BillOfMaterials::find($key)->item->description }}</div>
                </div>
            @endforeach
            <div>
                <div class="inline-block w-8"></div>
                <div class="inline-block w-8"><input type="radio" wire:model="input.bom_groups_values.{{ $prompt }}" value="0" class="dui-radio" /></div>
                <div class="inline-block">None</div>
            </div>
        @endforeach

        <div class="my-2">
            <button type="submit" class="dui-btn dui-btn-sm dui-btn-neutral" >Add to cart</button>
            <button type="button" class="dui-btn dui-btn-sm dui-btn-neutral" wire:click="cancel">Cancel</button>
        </div>
    </form>
</div>
