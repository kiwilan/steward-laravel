<?php

namespace Kiwilan\Steward\Utils;

use Illuminate\Support\Facades\File;
use Kiwilan\HttpPool\Utils\PrintConsole;

class Json
{
    protected ?string $path = null;

    protected mixed $contents = null;

    /**
     * @param  mixed  $data Can be a file path, json string, or array
     */
    public function __construct(
        readonly protected mixed $data,
    ) {
        if (is_file($this->data)) {
            $this->path = $this->data;
            $this->contents = file_get_contents($this->data);
        } elseif (is_string($this->data)) {
            $this->contents = json_decode($this->data, true);
        } else {
            $this->contents = $this->data;
        }
    }

    /**
     * @return string Pretty json string
     */
    public function pretty(): string
    {
        return json_encode($this->contents, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * @param  string  $saveTo Path to save the json file
     * @param  bool  $console Print save path to console
     */
    public function save(string $saveTo, bool $console = true): void
    {
        $pretty = $this->pretty();

        if (! is_dir($saveTo)) {
            mkdir($saveTo, recursive: true);
        }

        unlink($saveTo);
        file_put_contents($saveTo, $pretty);

        if ($console) {
            $print = PrintConsole::make();
            $print->print("Saved to `{$saveTo}`.");
        }
    }

    public function toArray(bool $is_associative = true): array
    {
        return json_decode($this->contents, $is_associative);
    }

    public function toObject(): object
    {
        return json_decode(json_encode($this->contents, JSON_FORCE_OBJECT));
    }

    public function __toString()
    {
        return json_encode($this->contents);
    }
}
