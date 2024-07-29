<?php

namespace Kiwilan\Steward\Filament\Config\FilamentLayout;

use Closure;
use Filament\Forms;

class FilamentLayoutColumn
{
    /**
     * @param  FilamentLayoutSection[]|mixed[]  $sections
     */
    public function __construct(
        protected array $sections = [],
        protected int $width = 2,
        protected bool|Closure $hidden = false,
    ) {}

    /**
     * @param  FilamentLayoutSection[]  $sections
     */
    public static function make(array $sections = []): self
    {
        $column = new FilamentLayoutColumn;
        $column->sections = $sections;

        return $column;
    }

    public function width(int $width = 2): self
    {
        $this->width = $width;

        return $this;
    }

    public function hidden(bool|Closure $condition = true): self
    {
        $this->hidden = $condition;

        return $this;
    }

    public function get()
    {
        $fields = $this->setFields();

        return Forms\Components\Group::make()
            ->schema($fields)
            ->columnSpan([
                'sm' => 2,
                'xl' => $this->width,
            ])
            ->hidden($this->hidden);
    }

    private function setFields(): array
    {
        $fields = [];

        foreach ($this->sections as $key => $section) {
            $component = Forms\Components\Section::make();
            $schema = $section instanceof FilamentLayoutSection ? $section->get() : $section;

            $fields[] = $component
                ->schema($schema)
                ->columns([
                    'sm' => $this->width,
                ]);
        }

        return $fields;
    }
}
