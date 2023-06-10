<div>
    <x-slot name="header">
        <x-page.header>
            {{ __('Cart') }}
        </x-page.header>
    </x-slot>

    <x-page.content>
        {{ $this->table }}

        <div class="my-2">
            <a href="{{route('catalog.show')}}" class="dui-btn dui-btn-neutral">{{ __('Return to Catalog') }}</a>
            <a href="{{route('checkout')}}" class="dui-btn dui-btn-neutral">{{ __('Checkout') }}</a>
        </div>
    </x-page.content>
</div>
