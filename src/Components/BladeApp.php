<?php

namespace Kiwilan\Steward\Components;

use Illuminate\View\Component;

class BladeApp extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public mixed $head = null,
        public bool $dark = false,
        public string $tile = '#da532c',
        public string $theme = '#ffffff',
    ) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('steward::components.app');
    }
}
