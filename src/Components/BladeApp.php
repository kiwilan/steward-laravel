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
        public bool $inertia = false,
        public array $page = [],
        public bool $routes = false,
        public string $tile = '#da532c',
        public string $theme = '#ffffff',
        public bool $darkMode = false,
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('steward::components.app');
    }
}
