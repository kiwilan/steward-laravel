<?php

namespace Kiwilan\Steward\Filament\Config\FilamentLayout;

use Filament\Forms\Components\Component;

class FilamentLayoutSection
{
    protected function __construct(
        protected array $fields = [],
    ) {}

    /**
     * @param  Component[]  $fields
     */
    public static function make(array $fields = []): self
    {
        $column = new FilamentLayoutSection();
        $column->fields = $fields;

        return $column;
    }

    public function get(): array
    {
        return $this->fields;
    }
}
