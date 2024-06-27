<?php

namespace Kiwilan\Steward\Components;

use Illuminate\View\Component;

class Button extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $type = 'button',
        public ?string $href = null,
        public bool $external = false,
        public mixed $slot = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('steward::components.button');
    }
}
