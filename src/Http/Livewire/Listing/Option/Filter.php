<?php

namespace Kiwilan\Steward\Http\Livewire\Listing\Option;

use Livewire\Component;

class Filter extends Component
{
    public string $label = 'Filter';

    public string $name;

    public mixed $current = []; // current query string to fill when refreshing

    public string $query = ''; // query string like `filter[category]`

    public array $options = []; // all options for this filter

    public bool $expanded = false;

    public bool $border = false;

    public bool $unique = false;

    protected $listeners = ['clearFilter'];

    public function clearFilter()
    {
        $this->current = [];
    }

    public function mount()
    {
        $this->query = "filter[{$this->name}]";
    }

    public function updatedCurrent()
    {
        $this->emitUp('query', $this->query, $this->current);
    }

    public function render()
    {
        return view('steward::livewire.listing.option.filter');
    }
}
