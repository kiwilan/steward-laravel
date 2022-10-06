<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Kiwilan\Steward\Queries\BaseQuery;
use Kiwilan\Steward\Queries\Options\ClassMetadata;
use Spatie\QueryBuilder\QueryBuilder;

class HttpQuery extends BaseQuery
{
    /**
     * @param EloquentBuilder|Relation|string $class
     * @param Request|null                    $request
     */
    public static function make($class, ?Request $request = null): self
    {
        $query = new HttpQuery();
        $query->metadata = ClassMetadata::create($class);
        $query->resource = $query->metadata->class_plural_snake;
        $query->request = $request;

        $query->setQuery();

        return $query;
    }

    /**
     * Set a resource like `PostResource::class`.
     */
    public function resource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function with(array $with = []): self
    {
        $this->with = $with;

        return $this;
    }

    public function withCount(array $withCount = []): self
    {
        $this->withCount = $withCount;

        return $this;
    }

    /**
     * @param string $orderBy   Any `fillable`, default is `id`
     * @param string $direction `asc` | `desc`
     */
    public function orderBy(string $orderBy = 'id', string $direction = 'desc'): self
    {
        $this->orderBy = $orderBy;
        $this->sortAsc = 'asc' === $direction ? true : false;

        return $this;
    }

    public function exportable(): self
    {
        $this->exportable = true;

        return $this;
    }

    public function collection(): AnonymousResourceCollection
    {
        return $this->getCollection();
    }

    public function get(): array
    {
        return [
            'sort' => request()->get('sort', $this->sortDefault),
            'filter' => request()->get('filter'),
            'books' => fn () => $this->collection(),
        ];
    }

    private function setQuery()
    {
        // if (! $option || null === $option->resource) {
        //     $option = new QueryOption(resource: BookResource::class);
        // }

        // $this->option = $option;
        // $option->with = [] === $option->with ? ['serie', 'media', 'authors', 'language', 'publisher', 'tags', 'googleBook'] : $this->option->with;

        $this->query = QueryBuilder::for($this->metadata->class)
            ->defaultSort($this->sortDefault)
            ->allowedFilters($this->allowFilters
                // AllowedFilter::custom('q', new GlobalSearchFilter(['title', 'serie'])),
                // AllowedFilter::exact('id'),
                // AllowedFilter::partial('title'),
                // AllowedFilter::callback('serie', function (Builder $query, $value) {
                //     return $query->whereHas('serie', function (Builder $query) use ($value) {
                //         $query->where('title', 'like', "%{$value}%");
                //     });
                // }),
                // AllowedFilter::partial('volume'),
                // AllowedFilter::callback('authors', function (Builder $query, $value) {
                //     return $query->whereHas('authors', function (Builder $query) use ($value) {
                //         $query->where('name', 'like', "%{$value}%");
                //     });
                // }),
                // AllowedFilter::exact('is_disabled'),
                // AllowedFilter::exact('released_on'),
                // AllowedFilter::exact('type'),
                // AllowedFilter::scope('types', 'whereTypesIs'),
                // AllowedFilter::callback('language', function (Builder $query, $value) {
                //     return $query->whereHas('language', function (Builder $query) use ($value) {
                //         $query->where('name', 'like', "%{$value}%");
                //     });
                // }),
                // AllowedFilter::scope('languages', 'whereLanguagesIs'),
                // AllowedFilter::callback('publisher', function (Builder $query, $value) {
                //     return $query->whereHas('publisher', function (Builder $query) use ($value) {
                //         $query->where('name', 'like', "%{$value}%");
                //     });
                // }),
                // AllowedFilter::scope('disallow_serie', 'whereDisallowSerie'),
                // AllowedFilter::scope('language', 'whereLanguagesIs'),
                // AllowedFilter::scope('published', 'publishedBetween'),
                // AllowedFilter::scope('is_disabled', 'whereIsDisabled'),
                // AllowedFilter::scope('author_like', 'whereAuthorIsLike'),
                // AllowedFilter::scope('tags_all', 'whereTagsAllIs'),
                // AllowedFilter::scope('tags', 'whereTagsIs'),
                // AllowedFilter::scope('isbn', 'whereIsbnIs'),
            )
            ->allowedSorts($this->allowSorts)
            ->with($this->with)
            ->withCount($this->withCount)
        ;

        // if ($this->option->withExport) {
        //     $this->export = new BookExport($this->query);
        // }

        return $this;
    }
}
