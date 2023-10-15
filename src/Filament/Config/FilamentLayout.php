<?php

namespace Kiwilan\Steward\Filament\Config;

use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutColumn;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutSection;

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
        return new self($form);
    }

    /**
     * @param  FilamentLayoutSection[]  $sections
     */
    public static function column(array $sections = [], int $width = 2): \Filament\Forms\Components\Group
    {
        return FilamentLayoutColumn::make($sections)
            ->width($width)
            ->get()
        ;
    }

    /**
     * @param  Component[]  $fields
     */
    public static function section(array $fields = []): FilamentLayoutSection
    {
        return FilamentLayoutSection::make($fields);
    }

    public function width(int $width = 3): self
    {
        $this->width = $width;

        return $this;
    }

    public function schema(array $schema = []): Form
    {
        return $this->form
            ->schema($schema)
            ->columns([
                'xl' => $this->width,
            ])
        ;
    }
}
