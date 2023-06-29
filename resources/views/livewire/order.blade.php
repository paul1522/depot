<div class="w-full">
    <x-slot name="header">
        <x-page.header>
            {{ __('Order') . ' # ' . $order->id }}
        </x-page.header>
    </x-slot>

    <x-page.content>

        <x-page.section>
        {{ $this->form }}
        </x-page.section>

        <x-page.section>
        {{ $this->table }}
        </x-page.section>
    </x-page.content>
</div>
