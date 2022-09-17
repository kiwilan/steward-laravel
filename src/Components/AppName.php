<?php

namespace Kiwilan\Steward\Components;

use Illuminate\View\Component;

class AppName extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $name = 'text',
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('steward::components.app-name');
    }
}
