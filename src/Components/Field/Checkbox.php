<?php

namespace Kiwilan\Steward\Components\Field;

use Illuminate\View\Component;

class Checkbox extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name = 'checkbox',
        public string $label = '',
        public bool $checked = false,
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.field.checkbox');
    }
}
