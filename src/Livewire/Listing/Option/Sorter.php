<?php

namespace Kiwilan\Steward\Livewire\Listing\Option;

use Livewire\Component;

class Sorter extends Component
{
    public string $label = 'Sorter';

    public string $labelCurrent = 'current';

    public string $name;

    public string $current = ''; // current query string to fill when refreshing

    public string $query = ''; // query string like `filter[category]`

    public array $options = []; // all options for this filter

    public bool $isDesc = false;

    protected $listeners = ['clearSorter'];

    public function clearSorter()
    {
        $this->current = '';
    }

    public function mount()
    {
        $this->query = 'sort';
        $this->convertLabel();
    }

    public function convertLabel()
    {
        $current = $this->current;

        if (substr($current, 0, 1) === '-') {
            $current = substr($current, 1);
            $this->isDesc = true;
        }

        if (array_key_exists($current, $this->options)) {
            $this->labelCurrent = $this->options[$current];
        }

        if ($this->isDesc) {
            $this->labelCurrent .= ' (desc)';
        }
    }

    public function select(string $value)
    {
        $this->current = $value;
        $this->convertLabel();
        $this->setDesc();

        $this->dispatch('query', $this->query, $this->current);
    }

    public function setDesc()
    {
        if (substr($this->current, 0, 1) === '-') {
            $this->isDesc = true;
        } else {
            $this->isDesc = false;
        }
    }

    public function reverseSort()
    {
        if (substr($this->current, 0, 1) === '-') {
            $this->current = substr($this->current, 1);
        } else {
            $this->current = "-{$this->current}";
        }

        $this->setDesc();
        $this->select($this->current);
    }

    public function render()
    {
        return view('steward::livewire.listing.option.sorter');
    }
}
