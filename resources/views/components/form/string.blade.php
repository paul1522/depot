@props(['label'])
<div class="dui-form-control w-full">
    <label class="label"><span class="dui-label-text">{{ $label }}</span></label>
    <input readonly type="text" class="dui-input dui-input-bordered w-full" value="{{ $slot }}"/>
</div>
