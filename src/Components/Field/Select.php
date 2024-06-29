<?php

namespace Kiwilan\Steward\Components\Field;

use Illuminate\View\Component;

class Select extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name = 'select',
        public string $label = '',
        public ?string $default = null,
        public array $options = [],
    ) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if ($this->default) {
            array_unshift($this->options, $this->default);
        }

        return view('steward::components.field.select');
    }
}
