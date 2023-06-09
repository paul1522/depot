<div class="item-location">
    <x-grid>

        <x-form.string label="Key">{{ $itemLocation->item->key }}</x-form.string>
        <x-form.string label="Supplier Key">{{ $itemLocation->item->supplier_key }}</x-form.string>
        <x-form.string label="Group">{{ $itemLocation->item->group }}</x-form.string>
        <x-form.string label="Manufacturer">{{ $itemLocation->item->manufacturer }}</x-form.string>
        <x-form.string label="Quantity Available">{{ $itemLocation->quantity }}</x-form.string>

    </x-grid>
</div>
