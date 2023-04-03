<?php

namespace Kiwilan\Steward\Filament\Config\FilamentLayout;

use Filament\Forms;
use Kiwilan\Steward\Filament\Config\FilamentLayout;

class FilamentLayoutSettings
{
    public function __construct(
        protected array $fields = [],
        protected int $width = 1,
        protected ?string $title = null,
    ) {
    }

    public static function make(array $fields, int $width = 2, ?string $title = null)
    {
        $self = new self($fields, $width, $title);

        return Forms\Components\Group::make()
            ->schema([
                FilamentLayout::card($self->fields, $self->title),
            ])
            ->columnSpan([
                'sm' => 1,
                'lg' => $self->width,
            ])
        ;
    }
}
