@if($image['webp_srcset'] || $image['fallback_srcset'])
<picture>
    @if($image['webp_srcset'])
    <source type="image/webp"
            srcset="{{ $image['webp_srcset'] }}"
            sizes="{{ $image['sizes'] }}">
    @endif
    <img src="{{ $image['src'] }}"
         alt="{{ $alt }}"
         @if($image['fallback_srcset']) srcset="{{ $image['fallback_srcset'] }}" @endif
         sizes="{{ $image['sizes'] }}"
         @if($image['width']) width="{{ $image['width'] }}" @endif
         @if($image['height']) height="{{ $image['height'] }}" @endif
         loading="{{ $loading }}"
         decoding="{{ $decoding }}"
         @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
         class="{{ $class }}">
</picture>
@elseif($src)
<img src="{{ $src }}"
     alt="{{ $alt }}"
     loading="{{ $loading }}"
     decoding="{{ $decoding }}"
     @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
     class="{{ $class }}">
@endif
