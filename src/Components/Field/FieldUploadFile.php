<?php

namespace Kiwilan\Steward\Components\Field;

use Illuminate\View\Component;

class FieldUploadFile extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $name = 'file',
        public ?string $label = null,
        public bool $multiple = false,
        public ?string $accept = 'image/jpeg,image/png,image/webp',
        public ?string $accepted = null,
        public ?string $size = '1MB',
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('steward::components.field.upload-file');
    }
}
