<div class="w-full">
    <x-slot name="header">
        <x-page.header>
            {{ __('Order Status Report') }}
        </x-page.header>
    </x-slot>

    <x-page.content>
        {{ $this->table }}
    </x-page.content>
</div>
