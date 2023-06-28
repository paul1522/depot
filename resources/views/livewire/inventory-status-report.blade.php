<div>
    <x-slot name="header">
        <x-page.header>
            {{ __('Inventory Status Report') }}
        </x-page.header>
    </x-slot>

    <x-page.wide-content>
        {{ $this->table }}
    </x-page.wide-content>
</div>
