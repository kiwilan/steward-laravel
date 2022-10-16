<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder;

use Filament\Forms;

class FilamentBuilderBlock
{
    public function __construct(
        protected array $fields,
        protected string $name = 'block',
        protected ?string $icon = null,
        protected int $columns = 2,
    ) {
    }

    public static function make(array $fields): self
    {
        return new FilamentBuilderBlock($fields);
    }

    public function get()
    {
        $block = Forms\Components\Builder\Block::make($this->name)
            ->schema([
                ...$this->fields,
            ]);

        if ($this->icon) {
            $block->icon($this->icon);
        }

        return $block->columns($this->columns);
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function columns(int $columns): self
    {
        $this->columns = $columns;

        return $this;
    }
}
