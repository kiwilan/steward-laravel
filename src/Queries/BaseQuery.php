<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kiwilan\Steward\Resources\DefaultResource;
use Kiwilan\Steward\Services\ClassParser\ClassParserItem;
use Kiwilan\Steward\Services\ClassParserService;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @template T
 */
abstract class BaseQuery
{
    private ?string $class = null;

    private ?Model $instance = null;

    private ?Builder $builder = null;

    private ?ClassParserItem $parser = null;

    private ?Request $request = null;

    /**
     * Spatie Query builder instance.
     */
    private Builder|QueryBuilder|null $query;

    /**
     * Model relations to eager load.
     */
    protected array $with = [];

    /**
     * Model relations to count.
     */
    protected array $withCount = [];

    /**
     * Default sorter field.
     */
    protected string $defaultSort = 'id';

    /**
     * Default allowed filters.
     */
    protected array $allowFilters = [];

    /**
     * Default allowed sorters.
     */
    protected array $allowSorts = [];

    /**
     * Class to use for export.
     */
    protected ?string $export = null;

    /**
     * `Illuminate\Http\Resources\Json\JsonResource` to use.
     */
    protected ?string $resource = null;

    /**
     * Disable pagination.
     */
    protected bool $noPaginate = false;

    /**
     * Default pagination limit.
     */
    protected int $pagination = 15;

    protected function setup(string|Builder $model, Request $request = null): self
    {
        if (! \Composer\InstalledVersions::isInstalled('spatie/laravel-query-builder')) {
            throw new \Exception('Package `spatie/laravel-query-builder` not installed, see: https://github.com/spatie/laravel-query-builder');
        }

        if ($model instanceof Builder) { // Instance of Builder from model
            $instance = $model->getModel();
            $this->class = get_class($instance);
            $this->instance = new $instance();
            $this->builder = $model;
        } else {
            $this->class = $model;
            $this->instance = new $model();
            $this->builder = $this->class::query();

            if (! $this->instance instanceof Model) {
                throw new \Exception('$model must be an instance of Illuminate\Database\Eloquent\Model');
            }
        }

        $this->request = $request;
        $this->parser = ClassParserService::make($this->class);
        $this->exportGuess();
        $this->resourceGuess();

        $this->defaultConfig();
        $this->query = QueryBuilder::for($this->builder);

        return $this;
    }

    /**
     * Get data to show into view.
     */
    private function queryReponse(): QueryResponse
    {
        $this->loadRequest();

        return \Kiwilan\Steward\Queries\QueryResponse::make(
            original: $this->paginate(),
            defaultSort: $this->defaultSort
        );
    }

    /**
     * @param  \Closure(\Kiwilan\Steward\Queries\QueryResponse): (mixed)  $closure
     */
    public function closure(\Closure $closure): mixed
    {
        return $closure($this->queryReponse());
    }

    /**
     * Export data to Excel.
     *
     * @param  string|null  $path  Path to save file, if null, return file.
     */
    public function export(string $path = null): BinaryFileResponse|string|null
    {
        $this->loadRequest();
        $this->exportGuess();

        return ExportQuery::make(
            query: $this,
            path: $path
        )->export();
    }

    /**
     * Get API response.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(): Collection
    {
        $this->loadRequest();

        return $this->query->get();
    }

    /**
     * Get API response paginated.
     *
     * - Property `noPaginate` on `true` in model or with `noPaginate()` method on `HttpQuery` to disable pagination.
     * - Query params `full` or `no-paginate` allow to disable pagination.
     * - Query param `limit` can be used to set pagination limit (will override default limit).
     * - Default pagination limit is set in model with `pagination` property.
     * - Max pagination limit is 100.
     */
    public function paginate(): LengthAwarePaginator|Collection
    {
        $this->loadRequest();
        $full = $this->request->boolean('full') || $this->request->boolean('no-paginate');

        if ($this->noPaginate || $full) {
            return $this->query->get();
        }

        $pagination = min(100, request()->get('limit', $this->pagination));

        return $this->query->paginate($pagination);
    }

    /**
     * Get API resource.
     *
     * - Default resource can be set on model with `queryResource` property.
     * - Can be set on `HttpQuery` with `resource()` method.
     * - Auto guess resource from model name, if not already set.
     * - If not exists, use `DefaultResource`.
     */
    public function collection(): AnonymousResourceCollection
    {
        $this->loadRequest();

        if (! class_exists($this->resource)) {
            $this->resource = DefaultResource::class;
        }

        /** @var JsonResource $resource */
        $resource = $this->resource;

        return $resource::collection($this->paginate());
    }

    /**
     * Class instance, like `Book::class`.
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Model instance, like `new Book()`.
     */
    public function getInstance(): Model
    {
        return $this->instance;
    }

    /**
     * Query builder instance, like `Book::query()`.
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * Class parser instance.
     */
    public function getParser(): ClassParserItem
    {
        return $this->parser;
    }

    /**
     * Request instance.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getOptions(): array
    {
        return [
            'with' => $this->with,
            'withCount' => $this->withCount,
            'defaultSort' => $this->defaultSort,
            'allowFilters' => $this->allowFilters,
            'allowSorts' => $this->allowSorts,
            'export' => $this->export,
            'resource' => $this->resource,
            'noPaginate' => $this->noPaginate,
            'pagination' => $this->pagination,
        ];
    }

    /**
     * Spatie Query builder instance.
     */
    public function getQuery(): Builder|QueryBuilder|null
    {
        return $this->query;
    }

    protected function loadRequest(): self
    {
        $this->query = QueryBuilder::for($this->builder, $this->request)
            ->allowedFilters($this->allowFilters)
            ->allowedSorts($this->allowSorts)
            ->defaultSort($this->defaultSort)
            ->with($this->with)
            ->withCount($this->withCount)
        ;

        return $this;
    }

    /**
     * Guess export class from `App\Exports\{ClassName}Export`.
     */
    private function exportGuess(): self
    {
        $name = $this->parser->getMeta()->getClassName();
        $export_class = "App\\Exports\\{$name}Export";

        if (! $this->export && class_exists($export_class)) {
            $this->export = $export_class;
        }

        return $this;
    }

    /**
     * Set default config for Spatie Query Builder if not set.
     */
    private function defaultConfig(): void
    {
        if (config('query-builder') === null) {
            config(['query-builder.parameters.include' => 'include']);
            config(['query-builder.parameters.filter' => 'filter']);
            config(['query-builder.parameters.sort' => 'sort']);
            config(['query-builder.parameters.fields' => 'fields']);
            config(['query-builder.parameters.append' => 'append']);
            config(['query-builder.count_suffix' => 'Count']);
            config(['query-builder.disable_invalid_filter_query_exception' => false]);
            config(['query-builder.request_data_source' => 'query_string']);
        }
    }

    /**
     * Guess API Resource.
     */
    private function resourceGuess(): self
    {
        $name = $this->parser->getMeta()->getClassName();

        $ressources = [
            "{$name}\\{$name}CollectionResource",
            "{$name}CollectionResource",
            "{$name}\\{$name}Collection",
            "{$name}Collection",
            "{$name}\\{$name}Resource",
            "{$name}Resource",
        ];

        if (! $this->resource) {
            foreach ($ressources as $resource) {
                $resource = "App\\Http\\Resources\\{$resource}";

                if (class_exists($resource)) {
                    $this->resource = $resource;

                    return $this;
                }
            }
        }

        return $this;
    }
}
