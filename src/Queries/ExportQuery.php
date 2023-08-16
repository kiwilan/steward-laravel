<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportQuery
{
    protected function __construct(
        protected string $type, // standard, excel
        protected array $data,
        protected QueryBuilder $query,
        protected string $filename = 'export',
        protected string $extension = 'csv',
        protected ?string $export = null,
        protected ?string $path = null,
        protected bool $toSave = false,
        protected bool $skipExcel = false,
    ) {
    }

    public static function make(Collection $data, QueryBuilder $query, string $name, string $export = null, string $path = null, bool $skipExcel = false): self
    {
        $filename = $name;
        $date = date('Ymd-His');
        $filename = "export-{$filename}-{$date}";

        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        $self = new self(
            type: 'standard',
            data: $data,
            query: $query,
            filename: $filename,
            export: $export,
            path: $path,
            toSave: $path !== null,
            skipExcel: $skipExcel,
        );
        $self->type = $self->selectType();

        if ($skipExcel) {
            $self->type = 'standard';
        }

        return $self;
    }

    public function export(): Response|BinaryFileResponse|bool
    {
        if ($this->type === 'standard') {
            return $this->exportStandard();
        }

        if ($this->type === 'excel') {
            return $this->exportExcel();
        }

        return false;
    }

    private function exportStandard(): Response|bool
    {
        $this->extension = 'csv';
        $filename = "{$this->filename}.{$this->extension}";
        $fh = fopen('php://temp', 'rw');

        fputcsv($fh, array_keys(current($this->data)));

        foreach ($this->data as $row) {
            $row = $this->arrayFlatten($row);
            fputcsv($fh, $row);
        }
        rewind($fh);
        $contents = stream_get_contents($fh);
        fclose($fh);

        if ($this->toSave) {
            $success = file_put_contents("{$this->path}/{$filename}", $contents);

            return gettype($success) === 'integer';
        }

        return response()
            ->make(
                content: $contents,
                status: 200,
                headers: [
                    'Content-Type' => 'text/csv',
                    'charset' => 'UTF-8',
                    'Content-Disposition' => "attachment; filename={$filename}",
                    'Pragma: no-cache',
                    'Expires: 0',
                ])
        ;
    }

    private function exportExcel(): BinaryFileResponse|bool
    {
        if (! \Composer\InstalledVersions::isInstalled('maatwebsite/excel')) {
            throw new \Exception('Package maatwebsite/excel not installed, see https://github.com/SpartnerNL/Laravel-Excel');
        }

        $this->extension = 'xlsx';
        $export = new $this->export($this->query);

        if ($this->toSave) {
            return $export->store("{$this->path}/{$this->filename}.{$this->extension}");
        }

        return $export->download("{$this->filename}.{$this->extension}");
    }

    private function selectType(): string
    {
        return \Composer\InstalledVersions::isInstalled('maatwebsite/excel') ? 'excel' : 'standard';
    }

    private function arrayFlatten(array $array, string $prefix = null): ?array
    {
        if (! is_array($array)) {
            return null;
        }
        $result = [];

        foreach ($array as $key => $value) {
            $key = $prefix ? "{$prefix}_{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->arrayFlatten($value, $key));
            } elseif (is_object($value)) {
                $result = array_merge($result, $this->arrayFlatten((array) $value, $key));
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
