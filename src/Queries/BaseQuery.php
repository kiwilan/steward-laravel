<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Kiwilan\Steward\Class\MetaClass;
use Kiwilan\Steward\Resources\DefaultResource;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

abstract class BaseQuery
{
    public ?MetaClass $metadata = null;

    public ?Request $request = null;

    public array $with = [];

    public array $withCount = [];

    public string $defaultSort = 'id';

    public array $allowFilters = [];

    public array $allowSorts = [];

    protected Builder|QueryBuilder $query;

    protected ?string $export = null;

    protected ?string $resource = null;

    protected bool $full = false;

    protected int $limit = 15;

    public function paginate(): LengthAwarePaginator
    {
        return $this->query->paginate(min(100, request()->get('limit', $this->limit)));
    }

    /**
     * Get data to show into view.
     */
    public function get(): array
    {
        return [
            'sort' => request()->get('sort', $this->defaultSort),
            'filter' => request()->get('filter'),
            $this->metadata->class_snake_plural => fn () => $this->collection(),
        ];
    }

    /**
     * @return BinaryFileResponse|false
     *
     * @throws Exception
     * @throws WriterException
     */
    public function export()
    {
        $this->exportGuess();

        // $name = trans_choice("crud.{$this->resource}.name";
        $name = $this->metadata->class_snake_plural;
        $fileName = $name;
        $date = date('Ymd-His');

        if (class_exists(Excel::class)) {
            return Excel::download(new $this->export($this->query), "export-{$fileName}-{$date}.xlsx");
        }

        return false;
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
        $response = $this->request->boolean('full') || $this->full
            ? $this->query->get()
            : $this->paginate();

        return $resource::collection($response);
    }

    /**
     * Guess API Resource.
     * - `App\Http\Resources\{ClassName}\{ClassName}CollectionResource`
     * - `App\Http\Resources\{ClassName}CollectionResource`
     * - `App\Http\Resources\{ClassName}\{ClassName}Resource`
     * - `App\Http\Resources\{ClassName}Resource`.
     */
    public function resourceGuess(): self
    {
        $name = $this->metadata->class_name;

        $resource_classname_collection = "App\\Http\\Resources\\{$name}\\{$name}CollectionResource";
        $resource_collection = "App\\Http\\Resources\\{$name}CollectionResource";
        $resource_classname = "App\\Http\\Resources\\{$name}\\{$name}Resource";
        $resource = "App\\Http\\Resources\\{$name}Resource";

        if (! $this->resource) {
            if (class_exists($resource_classname_collection)) {
                $this->resource = $resource_classname_collection;
            } elseif (class_exists($resource_collection)) {
                $this->resource = $resource_collection;
            } elseif (class_exists($resource_classname)) {
                $this->resource = $resource_classname;
            } elseif (class_exists($resource)) {
                $this->resource = $resource;
            }
        }

        return $this;
    }

    /**
     * Guess export class from `App\Exports\{ClassName}Export`.
     */
    public function exportGuess(): self
    {
        $name = $this->metadata->class_name;
        $export_class = "App\\Exports\\{$name}Export";

        if (! $this->export && class_exists($export_class)) {
            $this->export = $export_class;
        }

        return $this;
    }
}
