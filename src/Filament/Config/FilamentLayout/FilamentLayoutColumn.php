<?php

namespace Kiwilan\Steward\Filament\Config\FilamentLayout;

use Filament\Forms;
use Illuminate\Support\Str;

class FilamentLayoutColumn
{
    public function __construct(
        protected array $fields = [],
        protected int $width = 2,
        protected bool $card = true,
        protected array $titles = [],
    ) {
    }

    /**
     * @param  array<array<int,mixed>>|array<int,mixed>  $fields
     */
    public static function make(array $fields = []): self
    {
        $column = new FilamentLayoutColumn();
        $column->fields = $fields;

        return $column;
    }

    public function width(int $width = 2): self
    {
        $this->width = $width;

        return $this;
    }

    public function disableCard(): self
    {
        $this->card = false;

        return $this;
    }

    public function titles(string|array $titles = []): self
    {
        $this->titles = is_array($titles) ? $titles : [$titles];

        return $this;
    }

    public function get()
    {
        $fields = $this->setFields();

        return Forms\Components\Group::make()
            ->schema($fields)
            ->columnSpan([
                'sm' => $this->width,
            ]);
    }

    private function setFields(): array
    {
        $fields = [];
        foreach ($this->fields as $key => $field) {
            if (! is_array($field)) {
                $field = [$field];
            }

            $title = null;
            if (array_key_exists($key, $this->titles)) {
                $title = $this->titles[$key];
            }

            $group = [];
            if ($title) {
                $group[] = Forms\Components\Placeholder::make(Str::slug($title))
                    ->label($title)
                    ->columnSpan($this->width);
            }
            $group = array_merge($group, $field);
            $component = $this->card ? Forms\Components\Card::make() : Forms\Components\Group::make();

            if (! empty($group)) {
                $fields[] = $component
                    ->schema($group)
                    ->columns([
                        'sm' => $this->width,
                    ]);
            } else {
                $fields[] = Forms\Components\Group::make();
            }
        }

        return $fields;
    }
}
