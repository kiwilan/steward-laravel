<?php

namespace Kiwilan\Steward\Components;

use Illuminate\View\Component;

class MetaTags extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?array $props = null,
        public string $key = 'meta',
        public string $twitter = 'summary_large_image', // summary, summary_large_image, app, player
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $url = null,
        public ?string $domain = null,
        public ?string $author = null,
        public ?string $color = '#000000',
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if (empty($this->props)) {
            $this->props = [
                $this->key => [],
            ];
        }

        if (empty($this->props[$this->key])) {
            $this->props[$this->key] = [
                'title' => config('app.name'),
                'description' => null,
                'image' => config('app.url').'/default.jpg',
                'color' => '#000000',
            ];
        }

        $this->title = $this->title ?? $this->props[$this->key]['title'];
        $this->description = $this->description ?? $this->props[$this->key]['description'];
        $this->image = $this->image ?? $this->props[$this->key]['image'];
        $this->color = $this->props[$this->key]['color'] ?? $this->color;

        if (! str_starts_with($this->color, '#')) {
            $this->color = "#{$this->color}";
        }

        if (strlen($this->title) > 70) {
            $this->title = substr($this->title, 0, 70).'...';
        }

        if (strlen($this->description) > 200) {
            $this->description = substr($this->description, 0, 200).'...';
        }

        if (! $this->author) {
            $this->author = config('app.name');
        }

        if (url()->current()) {
            $this->url = $this->url ?? url()->current();
            $this->domain = $this->domain ?? parse_url($this->url, PHP_URL_HOST);
        }

        return view('steward::components.meta-tags');
    }
}
