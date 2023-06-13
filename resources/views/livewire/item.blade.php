<div>
    <x-slot name="header">
        <x-page.header>
            {{ $itemLocation->item->description }}
        </x-page.header>
    </x-slot>

    <x-page.content>
        <x-page.section><livewire:page.section.show-item-location :itemLocation="$itemLocation" /></x-page.section>
        <x-page.section><livewire:page.section.show-order-form :itemLocation="$itemLocation" /></x-page.section>
        @if ($itemLocation->item->documents->count() > 0)
            <x-page.section><livewire:page.section.show-documents :itemLocation="$itemLocation" /></x-page.section>
        @endif
    </x-page.content>
</div>
