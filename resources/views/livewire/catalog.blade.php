<div>
    <x-slot name="header">
        <x-page.header>
            {{ __('Catalog') }}
        </x-page.header>
    </x-slot>

    <x-page.wide-content>
        {{ $this->table }}
    </x-page.wide-content>
</div>
