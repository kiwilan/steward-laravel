<?php

namespace Kiwilan\Steward\Components;

use Illuminate\View\Component;

class MetaTags extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('steward::components.meta-tags');
    }
}
