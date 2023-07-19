<?php

namespace Kiwilan\Steward\Components\Listing;

use Closure;
use Illuminate\View\Component;
use Illuminate\View\View;

class PaginationSize extends Component
{
    public function render(): Closure|View|string
    {
        return view('components.listing.pagination-size');
    }
}
