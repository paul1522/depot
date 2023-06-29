<div>
    <x-slot name="header">
        <x-page.header>
            {{ __('Inventory Status Report') }}
        </x-page.header>
    </x-slot>

    <x-page.content width="max-w-full">
        {{ $this->table }}
    </x-page.content>
</div>
