<?php

namespace Kiwilan\Steward\Components;

use Illuminate\View\Component;

class HeadMeta extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $dark = true,
        public string $tile = '#da532c',
        public string $theme = '#ffffff',
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('steward::components.head-meta');
    }
}
