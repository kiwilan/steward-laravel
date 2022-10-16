<?php

namespace Kiwilan\Steward\Filament\Config;

use Filament\Forms\Components\Card;
use Filament\Resources\Form;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutCard;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutColumn;

class FilamentLayout
{
    public function __construct(
        protected Form $form,
        protected int $width = 3,
        protected array $schema = [],
    ) {
    }

    public static function make(Form $form): self
    {
        return new FilamentLayout($form);
    }

    /**
     * @param  array<array<int,mixed>>|array<int,mixed>  $fields
     */
    public static function column(array $fields = []): FilamentLayoutColumn
    {
        return FilamentLayoutColumn::make($fields);
    }

    public static function card(array $fields = [], ?string $title = null): Card
    {
        return FilamentLayoutCard::make($fields, $title);
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
                'sm' => $this->width,
                'lg' => null,
            ]);
    }
}
