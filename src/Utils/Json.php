<?php

namespace Kiwilan\Steward\Utils;

use Kiwilan\HttpPool\Utils\PrintConsole;

class Json
{
    protected ?string $path = null;

    protected mixed $contents = null;

    /**
     * @param  mixed  $data  Can be a file path, json string, or array
     */
    public function __construct(
        protected readonly mixed $data,
    ) {
        if (is_array($data)) {
            $this->contents = $this->data;
        } elseif (is_string($data)) {
            if (file_exists($data)) {
                $this->path = $data;
                $this->contents = file_get_contents($data);
            } else {
                $this->contents = json_decode($data, true);
            }
        } else {
            $this->contents = $data;
        }
    }

    public static function load(string $path): self
    {
        return new self($path);
    }

    public function getContents(): mixed
    {
        return $this->contents;
    }

    /**
     * @return string Pretty json string
     */
    public function pretty(): string
    {
        return json_encode($this->contents, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * @param  string  $saveTo  Path to save the json file
     * @param  bool  $console  Print save path to console
     */
    public function save(string $saveTo, bool $console = true): void
    {
        $pretty = $this->pretty();

        if (file_exists($saveTo)) {
            unlink($saveTo);
        }

        if (! is_dir(dirname($saveTo))) {
            mkdir(dirname($saveTo), recursive: true);
        }

        file_put_contents($saveTo, $pretty);

        if ($console) {
            $print = PrintConsole::make();
            $print->print("Saved to `{$saveTo}`.");
        }
    }

    public function toArray(bool $is_associative = true): array
    {
        if (is_null($this->contents)) {
            return [];
        }

        if (is_array($this->contents)) {
            return $this->contents;
        }

        $data = json_decode($this->contents, $is_associative);
        if (is_null($data)) {
            return [];
        }

        return $data;
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
