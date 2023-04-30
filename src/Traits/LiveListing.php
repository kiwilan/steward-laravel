<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Facades\Session;

/**
 * For Livewire component with model uses LiveFiltering.
 */
trait LiveListing
{
    public const PAGINATION = 'tailwind';

    public string $q = '';

    public string $sort = 'created_at';

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

        $defaultSize = config('steward.livewire.pagination.size', 20);
        $this->size = Session::get('size', $defaultSize);
        $this->sort = ! empty($this->sort) ? $this->sort : $this->defaultSort();
        $this->sortable = $this->sortable();
    }

    abstract public function model(): string;

    abstract public function relations(): array;

    abstract public function defaultSort(): string;

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

    public function clear()
    {
        $this->reset(['q', 'sort', 'filter', 'page', 'size']);
        $this->sort = $this->defaultSort();
        $this->emit('clearFilter');
        $this->emit('clearSorter');
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
            ->toArray()
        ;
    }
}
