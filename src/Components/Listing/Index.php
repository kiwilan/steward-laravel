<?php

namespace Kiwilan\Steward\Components\Listing;

use Closure;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

class Index extends Component
{
    public const SIZES = [
        10,
        20,
        50,
        100,
    ];

    public function __construct(
        public string $title = 'Title',
        public ?string $subtitle = null,
        public bool $filterable = true,
        public array|false $sortable = false,
        public string $sort = 'created_at',
        public bool $searchable = false,

        public mixed $paginate = null,
        public array $paginationSizeOptions = [],

        public mixed $filters = null, // slot
        public mixed $sorters = null, // slot
    ) {
    }

    public function render(): Closure|View|string
    {
        $name = Str::slug($this->title);
        $this->paginationSizeOptions = config('steward.livewire.pagination.options') ?? self::SIZES;

        return view('steward::components.listing.index', [
            'name' => $name,
        ]);
    }
}
