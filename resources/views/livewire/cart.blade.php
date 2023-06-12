<div>
    <x-slot name="header">
        <x-page.header>
            {{ __('Cart') }}
        </x-page.header>
    </x-slot>

    <x-page.content>

        {{ $this->table }}

        <div x-data="{ showDialog: false, showShipping: false, showCheckout: false, disableConfirm: true }">
        <div class="my-2">
            <div class="inline-block">
                <a href="{{route('catalog.show')}}" class="dui-btn dui-btn-neutral">{{ __('Return to Catalog') }}</a>
            </div>

            <div class="inline-block">
                <button class="dui-btn dui-btn-neutral" @click="showDialog=true;showShipping=true;showCheckout=false">Checkout</button>
            </div>
        </div>



        <dialog x-show="showDialog" id="shipping" class="dui-modal dui-modal-open" x-cloak>
            <form method="dialog" class="dui-modal-box">
                <div x-show="showShipping">
                    <h3 class="font-bold text-lg">Confirm shipping options</h3>
                    <div>
                        <div class="dui-form-control w-full max-w-xs">
                            <label class="dui-label"><span class="dui-label-text">Ship to location</span></label>
                            <select class="dui-select dui-select-bordered" @change="disableConfirm=false" wire:model="shipTo">
                                <option value="0" disabled selected>Pick one</option>
                                @foreach($shipTos as $location_id => $name)
                                    <option value="{{ $location_id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="dui-modal-action">
                            <button class="dui-btn dui-btn-neutral" :disabled="disableConfirm" @click="showCheckout=true;showShipping=false">Confirm</button>
                            <button class="dui-btn dui-btn-neutral" @click="showDialog=false">Cancel</button>
                        </div>
                    </div>
                </div>
                <div x-show="showCheckout">
                    <h3 class="font-bold text-lg">Checkout</h3>
                    <p class="py-4">Are you ready to place your order</p>
                    <div class="dui-modal-action">
                        <button class="dui-btn dui-btn-neutral" wire:click="submit">Yes</button>
                        <button class="dui-btn dui-btn-neutral" @click="showDialog=false">No</button>
                    </div>
                </div>
            </form>
        </dialog>
        </div>

    </x-page.content>
</div>
