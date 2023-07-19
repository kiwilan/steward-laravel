<?php

namespace Kiwilan\Steward\Components\Listing;

use Closure;
use Illuminate\View\Component;
use Illuminate\View\View;

class Sorters extends Component
{
    public function render(): Closure|View|string
    {
        return view('components.listing.sorters');
    }
}
