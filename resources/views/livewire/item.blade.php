<div class="w-full">
    <x-slot name="header">
        <x-page.header>
            {{ $item->description }}
        </x-page.header>
    </x-slot>

    <x-page.content>
        <x-page.section><livewire:page.section.show-item-location :item="$item" :location="$location" /></x-page.section>
        <x-page.section><livewire:page.section.show-order-form :item="$item" :location="$location" /></x-page.section>
        @if ($item->documents->count() > 0)
            <x-page.section><livewire:page.section.show-documents :item="$item" /></x-page.section>
        @endif
    </x-page.content>
</div>
