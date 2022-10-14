<?php

namespace Kiwilan\Steward\Filament\Config;

use Filament\Resources\Form;

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
