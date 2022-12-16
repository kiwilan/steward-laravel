<?php

namespace Kiwilan\Steward\Filament\Config\FilamentLayout;

use Filament\Forms;
use Filament\Forms\Components\Group;
use Kiwilan\Steward\Filament\Config\FilamentLayout;

class FilamentLayoutSettings
{
    public function __construct(
        protected array $fields = [],
        protected int $width = 1,
        protected ?string $title = null,
    ) {
    }

    public static function make(array $fields): self
    {
        return new FilamentLayoutSettings($fields);
    }

    public function width(int $width = 1): self
    {
        $this->width = $width;

        return $this;
    }

    public function title(?string $title = null): self
    {
        $this->title = $title;

        return $this;
    }

    public function get(): Group
    {
        return Forms\Components\Group::make()
            ->schema([
                FilamentLayout::card($this->fields, $this->title),
            ])
            ->columnSpan([
                'sm' => 1,
                'lg' => $this->width,
            ]);
    }
}
