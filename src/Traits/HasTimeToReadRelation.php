<?php

namespace Kiwilan\Steward\Traits;

trait HasTimeToReadRelation
{
    protected $default_time_to_read_relation_column = 'time_to_read';

    public function initializeHasTimeToReadRelation()
    {
        $relation = $this->getHasTimeToReadRelation();

        if ($relation) {
            $this->with[] = $relation;
        }
    }

    public function getHasTimeToReadRelation(): ?string
    {
        return $this->time_to_read_relation ?? null;
    }

    public function getTimeToReadRelationColumn(): string
    {
        return $this->time_to_read_relation_column ?? $this->default_time_to_read_relation_column;
    }

    public function getTimeToReadAttribute(): int
    {
        $relation = $this->getHasTimeToReadRelation();

        if (! $relation || ! $this->{$relation}) {
            return 0;
        }

        $model = $this->with($relation);
        $items = $this->{$relation}()->get();
        $times = array_map(
            fn ($item) => $item[$this->getTimeToReadRelationColumn()],
            $items->toArray()
        );

        return array_sum($times);
    }

    public function getTimeToReadMinutesAttribute(): int
    {
        return intval($this->time_to_read / 60);
    }
}
