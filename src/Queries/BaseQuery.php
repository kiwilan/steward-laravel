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
     * CLass to use for export.
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

        $this->query = QueryBuilder::for($this->builder);

        return $this;
    }

    /**
     * Get data to show into view.
     */
    public function get(): QueryResponse
    {
        return QueryResponse::make(
            original: $this->response()->toArray(),
            defaultSort: $this->defaultSort
        );
    }

    /**
     * @param  \Closure(QueryResponse): (mixed)  $closure
     */
    public function closure(\Closure $closure): mixed
    {
        return $closure($this->get());
    }

    public function export(): ?BinaryFileResponse
    {
        $this->exportGuess();

        // $name = trans_choice("crud.{$this->resource}.name";
        $name = $this->parser->getMeta()->getClassSnakePlural();
        $fileName = $name;
        $date = date('Ymd-His');

        // if (class_exists(\Composer\InstalledVersions::isInstalled('maatwebsite/excel'))) {
        //     return \Maatwebsite\Excel\Facades\Excel::download(new $this->export($this->query), "export-{$fileName}-{$date}.xlsx");        // } else {
        //     // todo: add export to csv
        //     // https://www.the-art-of-web.com/php/dataexport/
        //     throw new \Exception('Package maatwebsite/excel not installed, see https://github.com/SpartnerNL/Laravel-Excel');
        // }

        // TODO

        return null;
    }

    /**
     * Get API response.
     *
     * - If `noPaginate` option is present in model or when `HttpQuery` is called, return all data without pagination.
     * - If `full` or `no-paginate` query param is passed, return all data without pagination.
     */
    public function response(): LengthAwarePaginator|Collection
    {
        $this->loadRequest();
        $full = $this->request->boolean('full') || $this->request->boolean('no-paginate');

        return $full || $this->noPaginate
            ? $this->query->get()
            : $this->paginate();
    }

    /**
     * Get API resource.
     */
    public function collection(): AnonymousResourceCollection
    {
        if (! class_exists($this->resource)) {
            $this->resource = DefaultResource::class;
        }

        /** @var JsonResource $resource */
        $resource = $this->resource;

        return $resource::collection($this->response());
    }

    private function paginate(): LengthAwarePaginator
    {
        $pagination = min(100, request()->get('limit', $this->pagination));

        return $this->query->paginate($pagination);
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

            // if ($this->resource === null) {
            //     throw new \Exception("BaseQuery, resource not found for {$name}.");
            // }
        }

        return $this;
    }
}
