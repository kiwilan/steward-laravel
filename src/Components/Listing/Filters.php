<?php

namespace Kiwilan\Steward\Components\Listing;

use Closure;
use Illuminate\View\Component;
use Illuminate\View\View;

class Filters extends Component
{
    public function render(): Closure|View|string
    {
        return view('steward::components.listing.filters');
    }
}
