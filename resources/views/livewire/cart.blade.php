<div>
    <x-slot name="header">
        <x-page.header>
            {{ __('Cart') }}
        </x-page.header>
    </x-slot>

    <x-page.content>
        {{ $this->table }}
    </x-page.content>
</div>
