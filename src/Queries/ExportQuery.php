<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportQuery
{
    protected function __construct(
        protected string $type = 'standard', // standard, excel
        protected array $data = [],
        protected string $name = 'export',
        protected ?string $export = null,
        protected ?string $path = null
    ) {
    }

    public static function make(BaseQuery $query, string $path = null): self
    {
        $name = $query->getParser()->getMeta()->getClassSnakePlural();
        $date = date('Ymd-His');
        $name = "export-{$name}-{$date}";
        $data = $query->get();
        $export = $query->getOptions()['export'];

        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        $self = new self(
            data: $data,
            name: $name,
            export: $export,
            path: $path
        );
        $self->type = $self->selectType();

        return $self;
    }

    public function export(): ?BinaryFileResponse
    {
        if ($this->type === 'standard') {
            $this->exportStandard();
        }

        if ($this->type === 'excel') {
            $this->exportExcel();
        }

        return null;
    }

    private function exportStandard(): BinaryFileResponse
    {
        $fileName = "{$this->name}.csv";

        if ($this->path) {
            $fileName = $this->path.'/'.$fileName;

            if (! is_dir($this->path)) {
                mkdir($this->path, 0777, true);
            }

            if (! is_writable($this->path)) {
                throw new \Exception('Path is not writable');
            }

            if (file_exists($fileName)) {
                unlink($fileName);
            }

            $file = fopen($fileName, 'w');

            foreach ($this->data as $row) {
                // filter data
                array_walk($row, [$this, 'filterData']);

                fputcsv($file, $row, "\t");
            }

            fclose($file);

            return response()->download($fileName);
        }

        // headers for download
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        // header('Content-Type: application/vnd.ms-excel; charset=UTF-16LE');
        header('Content-Type: text/csv; charset=UTF-8');

        $flag = false;

        foreach ($this->data as $row) {
            if (! $flag) {
                // display column names as first row
                echo implode("\t", array_keys($row))."\n";
                $flag = true;
            }
            // filter data
            array_walk($row, [$this, 'filterData']);
            echo implode("\t", array_values($row))."\n";
        }

        exit;
    }

    private function exportExcel(): BinaryFileResponse
    {
        if (! \Composer\InstalledVersions::isInstalled('maatwebsite/excel')) {
            throw new \Exception('Package maatwebsite/excel not installed, see https://github.com/SpartnerNL/Laravel-Excel');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new $this->export, "{$this->name}.xlsx"); // @phpstan-ignore-line
    }

    private function selectType(): string
    {
        return \Composer\InstalledVersions::isInstalled('maatwebsite/excel') ? 'excel' : 'standard';
    }

    private function filterData(&$str)
    {
        // escape tab characters
        $str = preg_replace("/\t/", '\\t', $str);

        // escape new lines
        $str = preg_replace("/\r?\n/", '\\n', $str);

        // convert 't' and 'f' to boolean values
        if ($str == 't') {
            $str = 'TRUE';
        }

        if ($str == 'f') {
            $str = 'FALSE';
        }

        if (! is_string($str)) {
            $str = json_encode($str);
        }

        // force certain number/date formats to be imported as strings
        if (preg_match('/^0/', $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
            $str = "'$str";
        }

        // escape fields that include double quotes
        if (strstr($str, '"')) {
            $str = '"'.str_replace('"', '""', $str).'"';
        }

        $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
    }
}
