<?php

namespace Kiwilan\Steward\Components\Field;

use Illuminate\View\Component;

class Tiptap extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name = 'editor',
        public string $label = '',
        public array $options = [],
        public bool $footer = false,
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('steward::components.field.tiptap');
    }
}
