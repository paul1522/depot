<div>
    <x-slot name="header">
        <x-page.header>
            {{ __('Catalog') }}
        </x-page.header>
    </x-slot>

    <x-page.content>
        {{ $this->table }}
    </x-page.content>
</div>
