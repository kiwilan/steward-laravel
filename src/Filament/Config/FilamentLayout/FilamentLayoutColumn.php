<?php

namespace Kiwilan\Steward\Filament\Config\Archive\FilamentLayout;

use Filament\Forms;
use Illuminate\Support\Str;
use Kiwilan\Steward\Filament\Config\FilamentLayout;

class FilamentLayoutColumn
{
    public function __construct(
        // protected FilamentLayout $layout,
        // protected array $fields = [],
        protected int $width = 2,
        protected bool $card = true,
        // protected array $titles = [],
        // protected array $widths = [],
        // protected array $schema = [],
    ) {
    }

    public static function make(): self
    {
        return new FilamentLayoutColumn();
    }

    public function width(int $width = 2): self
    {
        $this->width = $width;

        return $this;
    }

    public function disableCard(): self
    {
        $this->card = false;

        return $this;
    }
}
