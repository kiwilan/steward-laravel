<?php

namespace App\View\Components\Field;

use Illuminate\View\Component;

class FieldQuill extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
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
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.field.quill');
    }
}
