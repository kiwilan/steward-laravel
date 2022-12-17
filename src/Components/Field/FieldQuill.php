<?php

namespace Kiwilan\Steward\Components\Field;

use Illuminate\Support\Str;
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
        public string $id = '',
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $this->id = Str::random(10);

        return view('steward::components.field.quill');
    }
}
