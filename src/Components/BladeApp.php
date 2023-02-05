<?php

namespace Kiwilan\Steward\Components;

use Illuminate\View\Component;

class BladeApp extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $vite = false,
        public bool $ziggy = false,
        public bool $inertia = false,
        public bool $darkMode = false,
    ) {
    }

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
