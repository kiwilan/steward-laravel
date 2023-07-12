<?php

namespace Kiwilan\Steward\Filament\Config;

use Filament\Forms\Components\Card;
use Filament\Resources\Form;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutCard;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutColumn;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutSettings;

class FilamentLayout
{
    public function __construct(
        protected Form $form,
        protected int $width = 3,
        protected array $schema = [],
    ) {
    }

    public static function make(Form $form, array $schema = [], int $width = 3): Form
    {
        $layout = new FilamentLayout($form);

        return $layout
            ->schema($schema)
            ->width($width)
            ->get()
        ;
    }

    /**
     * @param  array<array<int,mixed>>|array<int,mixed>  $fields
     */
    public static function column(array $fields = [], int $width = 2): \Filament\Forms\Components\Group
    {
        return FilamentLayoutColumn::make($fields)
            ->width($width)
            ->get()
        ;
    }

    public static function card(array $fields = [], string $title = null, int $width = 2): Card
    {
        return FilamentLayoutCard::make($fields, $title, $width);
    }

    public static function setting(array $fields = [], int $width = 2, string $title = null): \Filament\Forms\Components\Group
    {
        return FilamentLayoutSettings::make($fields, $width, $title);
    }

    public function width(int $width = 3): self
    {
        $this->width = $width;

        return $this;
    }

    public function schema(array $schema = []): self
    {
        $this->schema = $schema;

        return $this;
    }

    public function get(): Form
    {
        return $this->form
            ->schema($this->schema)
            ->columns([
                'xl' => $this->width,
            ])
        ;
    }
}
