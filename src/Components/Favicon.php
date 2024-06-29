<?php

namespace Kiwilan\Steward\Components;

use Illuminate\View\Component;

class Favicon extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $dark = true,
        public string $url = 'http://localhost:8000',
    ) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        $this->url = config('app.url');

        return view('steward::components.favicon');
    }
}
