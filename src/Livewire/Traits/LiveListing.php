<?php

namespace Kiwilan\Steward\Livewire\Traits;

use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Url;

/**
 * `Livewire\Component` trait to handle live listing. To use with model have trait `Kiwilan\Steward\Traits\LiveModelQueryable`.
 */
trait LiveListing
{
    public string $q = '';

    #[Url]
    public ?string $sort = null;

    public bool $sort_asc = true;

    public array $sortable = [];

    public int $size = 20;

    public function initializeLiveListing()
    {
        $this->listeners[] = 'query';
        $this->listeners[] = 'clear';
        $this->listeners[] = 'reverse';
        $this->listeners[] = 'paginationSize';

        $this->queryString['page'] = ['except' => 1];
        $this->queryString[] = 'q';
        $this->queryString[] = 'sort';
        $this->queryString[] = 'filter';
        $this->queryString[] = 'size';

        $defaultSize = config('steward.livewire.pagination.default', 20);
        $this->size = Session::get('size', $defaultSize);
        // $this->sort = ! empty($this->sort) ? $this->sort : $this->defaultSort;

        $this->sort = 'id';
        $this->sortable = $this->sortable();
        $this->sort_asc = ! str_contains($this->sort, '-');
    }

    // abstract public function model(): string;

    abstract public function relations(): array;

    // abstract public function defaultSort(): string;

    abstract public function sortable(): array;

    public function query(string $field, mixed $value)
    {
        if (str_contains($field, '[')) {
            $field = substr($field, 0, -1);
            $matches = explode('[', $field);

            $field = $matches[0];
            $subfield = $matches[1];
            $this->{$field}[$subfield] = $value;
        } else {
            $this->{$field} = $value;
        }
    }

    public function sorting(string $column): void
    {
        if ($this->sort === $column) {
            $this->reverse();
        } else {
            $this->query('sort', $column);
        }
    }

    public function reverse()
    {
        if (! $this->sort) {
            $this->sort = $this->defaultSort();
        }

        $this->sort = str_contains($this->sort, '-')
            ? substr($this->sort, 1)
            : '-'.$this->sort;
        $this->sort_asc = ! str_contains($this->sort, '-');
    }

    public function clear()
    {
        $this->reset(['q', 'sort', 'filter', 'page', 'size']);
        $this->sort = $this->defaultSort();
        $this->emit('clearFilter');
        $this->emit('clearSorter');
    }

    public function getPagination()
    {
        return 'tailwind';
    }

    public function paginationSize(mixed $size)
    {
        Session::put('size', $size);

        $this->size = intval($size);
        $this->page = 1;
    }

    public function setFilter(string $model, string $field = 'name', string $id = 'id'): array
    {
        return $model::query()
            ->pluck($field, $id)
            ->toArray();
    }
}
