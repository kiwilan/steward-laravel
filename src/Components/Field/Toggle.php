<?php

namespace Kiwilan\Steward\Components\Field;

use Illuminate\View\Component;

class Toggle extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name = 'toggle',
        public ?string $hint = null,
        public string $label = '',
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('steward::components.field.toggle');
    }
}
