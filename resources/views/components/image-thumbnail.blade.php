@props(['item' => $item])
<div class="text-center">
    @if ($item->image_path)
        <a href="{{ route('image', ['item' => $item]) }}" target="_blank">
            <img class="object-contain" src="{{ Storage::disk('public')->url($item->image_path) }}"  alt="Product image"/>
        </a>
    @else
        Product image not available
    @endif
</div>
