<?php

namespace Kiwilan\Steward\Components;

use Illuminate\View\Component;

class BladeApp extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public mixed $title = null,
        public bool $seo = false,
        public array|false|null $vite = ['resources/js/app.ts'],
        public bool $livewire = false,
        public array $inertia = [],
        public bool $inertiaEnabled = false,
        public array $page = [],
        public bool $ziggy = false,
        public bool $darkMode = false,
        public string $tile = '#da532c',
        public string $theme = '#ffffff'
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if (! empty($this->inertia)) {
            $this->inertiaEnabled = true;
            $this->page = $this->inertia;
        }

        return view('steward::components.app');
    }
}
