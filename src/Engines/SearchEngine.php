<?php

namespace Kiwilan\Steward\Engines;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kiwilan\Steward\Services\ClassParser\ClassParserItem;
use Kiwilan\Steward\Services\ClassParserService;

/**
 * Search Engine with laravel/scout
 * - https://laravel.com/docs/10.x/scout.
 */
class SearchEngine
{
    public array $results = [];

    public array $results_relevant = [];

    public function __construct(
        public int $top_limit = 3,
        public int $max_limit = 10,
        public int $count = 0,
        public string $search_type = 'collection',
        public ?string $q = null,
        public ?bool $relevant = false,
        public ?bool $opds = false,
        public ?array $types = [],
    ) {
    }

    /**
     * Create an instance of SearchEngine from query.
     */
    public static function create(?string $q = '', bool $relevant = false, bool $opds = false, string|array $types = null): SearchEngine
    {
        if ('string' === gettype($types)) {
            $types = explode(',', $types);
        }

        if (empty($types)) {
            $types = [
                'authors',
                'series',
                'books',
            ];
        }
        $engine = new SearchEngine(q: $q, relevant: $relevant, opds: $opds, types: $types);

        return $engine->searchEngine();
    }

    public function json(): JsonResponse
    {
        return response()->json([
            'data' => [
                'count' => empty($this->q) ? 0 : $this->count,
                'type' => $this->search_type,
                'query' => $this->q,
                'results' => empty($this->q) ? [] : (! $this->relevant ? $this->results : []),
                'results_relevant' => empty($this->q) ? [] : (! $this->relevant ? [] : $this->results_relevant),
            ],
        ]);
    }

    /**
     * Find search engine from laravel/scout and execute search.
     */
    public function searchEngine(): SearchEngine
    {
        $this->search_type = config('scout.driver');
        $this->search();

        /** @var Collection $collection */
        foreach ($this->results as $key => $collection) {
            $this->count += $collection->count();
        }

        if ($this->relevant) {
            $this->results_relevant = $this->getRelevantResults();
        }

        return $this;
    }

    /**
     * Get relevant results.
     *
     * @return array<string,array<string,array<string,mixed>>>
     */
    public function getRelevantResults(): array
    {
        /** @var Collection <string,mixed> */
        $list = collect();

        /** @var string $model */
        foreach ($this->results as $model => $results) {
            /** @var AnonymousResourceCollection $collection */
            $collection = $results;
            $json = $collection->toJson();
            $results_list = json_decode($json);
            $relevant_results = array_splice($results_list, 0, $this->top_limit);

            $list->put($model, [
                'relevant' => $relevant_results,
                'other' => $results_list,
            ]);
        }

        return $list->toArray();
    }

    /**
     * Search Entity[].
     */
    private function search(): SearchEngine
    {
        $items = ClassParserService::toCollection(app_path('Models'));

        foreach ($items as $item) {
            $this->entitySearch($item);
        }

        return $this;
    }

    private function entitySearch(ClassParserItem $item): void
    {
        $reflect = $item->getReflect();
        $static = $reflect->getName();
        $name = $reflect->getShortName();
        $key = Str::plural($name);

        $slug = preg_split('/(?=[A-Z])/', $name);
        $slug = implode('-', $slug);
        $key = Str::plural(Str::slug($slug));

        if (in_array($key, $this->types)) {
            $this->results[$key] =
                $static::search($this->q) // @phpstan-ignore-line
                    ->get()
            ;
        }
    }
}
