<div class="item-location flex flex-row space-x-8 items-center">
    <div class="basis-3/4">
    <x-grid>

        <x-form.string label="Key">{{ $item->key }}</x-form.string>
        <x-form.string label="Supplier Key">{{ $item->supplier_key }}</x-form.string>
        <x-form.string label="Group">{{ $item->group }}</x-form.string>
        <x-form.string label="Manufacturer">{{ $item->manufacturer }}</x-form.string>
        <x-form.string label="Quantity Available for {{ $location->name }}">{{
            \App\Models\ItemLocation::where('item_id', '=', $item->id)
            ->where('location_id', '=', $location->id)
            ->whereRaw('(condition_id in (select id from conditions where show_in_catalog))')
            ->sum('quantity')
        }}</x-form.string>

    </x-grid>
    </div>
    <div class="basis-1/4"><x-image-thumbnail :item="$item"></x-image-thumbnail></div>
</div>
