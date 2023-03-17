<?php

namespace Kiwilan\Steward\Services\Http\Utils;

use Illuminate\Support\Collection;

class HttpQuery
{
    protected function __construct(
        public string|int|null $identifier = null,
        public ?string $url = null,
    ) {
    }

    public function make(string|int $identifier, string $url): self
    {
        $this->identifier = $identifier;
        $this->url = $url;

        return $this;
    }

    /**
     * Create HttpQuery instance.
     *
     * @param  Collection<int,object>|string[]  $items
     * @return Collection<int|string,HttpQuery>
     */
    public function toArray(iterable $items, string $identifier = 'id', string $url = 'url'): Collection
    {
        $list = collect([]);

        foreach ($items as $key => $value) {
            $id = null;
            $url = null;

            if (is_object($value)) {
                $id = $value->{$identifier};
                $url = $value->{$url};
            }

            if (is_string($value)) {
                $id = $key;
                $url = $value;
            }

            $list->put($id, new self($id, $url));
        }

        return $list;
    }
}
