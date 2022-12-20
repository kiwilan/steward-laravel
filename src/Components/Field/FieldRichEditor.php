<?php

namespace Kiwilan\Steward\Components\Field;

use Illuminate\View\Component;

class FieldRichEditor extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $name = 'editor',
        public string $label = '',
        public string $hint = '',
        public string $helper = '',
        public bool $required = false,
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
        $this->id = $this->id ?: $this->getId();

        return view('steward::components.field.rich-editor');
    }

    private function getId(int $n = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
