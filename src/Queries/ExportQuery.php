<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportQuery
{
    protected function __construct(
        protected string $type = 'standard', // standard, excel
        protected array $data = [],
        protected string $filename = 'export',
        protected string $extension = 'csv',
        protected ?string $export = null,
        protected ?string $path = null,
        protected bool $toSave = false,
    ) {
    }

    public static function make(Collection $data, string $name, string $export = null, string $path = null): self
    {
        $filename = $name;
        $date = date('Ymd-His');
        $filename = "export-{$filename}-{$date}";

        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        $self = new self(
            data: $data,
            filename: $filename,
            export: $export,
            path: $path,
            toSave: $path !== null,
        );
        $self->type = $self->selectType();

        return $self;
    }

    public function export(): Response|BinaryFileResponse|null
    {
        if ($this->type === 'standard') {
            return $this->exportStandard();
        }

        if ($this->type === 'excel') {
            return $this->exportExcel();
        }

        return null;
    }

    private function exportStandard(): ?Response
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
            file_put_contents($this->path, $contents);

            return null;
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

    private function exportExcel(): ?BinaryFileResponse
    {
        if (! \Composer\InstalledVersions::isInstalled('maatwebsite/excel')) {
            throw new \Exception('Package maatwebsite/excel not installed, see https://github.com/SpartnerNL/Laravel-Excel');
        }

        $this->extension = 'xlsx';

        if ($this->toSave) {
            return \Maatwebsite\Excel\Facades\Excel::store(new $this->export, $this->path); // @phpstan-ignore-line
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new $this->export, "{$this->filename}.{$this->extension}"); // @phpstan-ignore-line
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
