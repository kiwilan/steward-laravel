<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Kiwilan\Steward\Utils\ClassMetadata;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

abstract class BaseQuery
{
    public ?ClassMetadata $metadata = null;

    public ?Request $request = null;

    public array $with = [];

    public array $withCount = [];

    public string $defaultSort = 'id';

    public array $allowFilters = [];

    public array $allowSorts = [];

    protected Builder|QueryBuilder $query;

    protected ?string $export = null;

    protected string $resource;

    protected int $size = 15;

    public function paginate(): LengthAwarePaginator
    {
        return $this->query->paginate(min(100, request()->get('size', $this->size)));
    }

    /**
     * Get data to show into view.
     */
    public function get(): array
    {
        return [
            'sort' => request()->get('sort', $this->defaultSort),
            'filter' => request()->get('filter'),
            $this->resource => fn () => $this->collection(),
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
        $name = $this->metadata->class_plural_snake;
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
        /** @var JsonResource $resource */
        $resource = $this->resource;
        $response = $this->request->boolean('full') ? $this->query->get() : $this->paginate();

        return $resource::collection($response);
    }

    /**
     * Guess API Resource from `App\Http\Resources\{ClassName}\{ClassName}CollectionResource`
     * or from `App\Http\Resources\{ClassName}`.
     */
    public function resourceGuess(): self
    {
        $name = $this->metadata->class_name;
        $resource_class = "App\\Http\\Resources\\{$name}\\{$name}CollectionResource";
        $resource_alternative_class = "App\\Http\\Resources\\{$name}Resource";

        if (! $this->resource) {
            if (class_exists($resource_class)) {
                $this->resource = $resource_class;
            } elseif (class_exists($resource_alternative_class)) {
                $this->resource = $resource_alternative_class;
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
