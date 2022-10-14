<?php

namespace Kiwilan\Steward\Filament\Config;

use Filament\Forms\Components\Field;
use Filament\Resources\Form;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutColumn;

class FilamentLayout
{
    public function __construct(
        protected Form $form,
        protected int $width = 3,
        public array $schema = [],
    ) {
    }

    public static function make(Form $form): self
    {
        return new FilamentLayout($form);
    }

    public function width(int $width = 3): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Add fields to schema.
     *
     * @param  Field[]|Field[][]  $fields
     */
    public function column(array $fields = []): FilamentLayoutColumn
    {
        foreach ($fields as $key => $field) {
            if (! is_array($field)) {
                $fields[$key] = [$field];
            }
        }

        return new FilamentLayoutColumn($this, $fields);
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
