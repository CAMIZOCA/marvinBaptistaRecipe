<?php

namespace App\View\Components\Public;

use App\Support\ResponsiveImage as ResponsiveImageSupport;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ResponsiveImage extends Component
{
    public array $image;

    public function __construct(
        public ?string $src = null,
        public string $alt = '',
        public string $class = '',
        public string $sizes = '100vw',
        public string $loading = 'lazy',
        public string $decoding = 'async',
        public ?string $fetchpriority = null,
        public array $widths = [],
    ) {
        $this->image = ResponsiveImageSupport::build($this->src, $this->widths, $this->sizes);
    }

    public function render(): View|Closure|string
    {
        return view('components.public.responsive-image');
    }
}
