<div>
    @if ($ad)
        <div class="ad {{ $position }} rounded-lg overflow-hidden shadow-lg">
            @if ($ad->url)
                <a href="#" wire:click.prevent="clickAd({{ $ad->id }})" class="block">
            @endif
            @if ($ad->image_url)
                <img src="{{ asset('storage/' . $ad->image_url) }}" alt="{{ $ad->name }}" class="w-full h-auto max-h-[100px] object-contain">
            @else
                <div class="p-4">
                    {!! $ad->content !!}
                </div>
            @endif
            @if ($ad->url)
                </a>
            @endif
        </div>
    @endif
</div>
