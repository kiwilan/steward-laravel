<?php

namespace Kiwilan\Steward\Filament\Config\Archive\FilamentLayout;

use Filament\Forms;
use Illuminate\Support\Str;
use Kiwilan\Steward\Filament\Config\FilamentLayout;

class ArchiveFilamentLayoutColumn
{
    public function __construct(
        protected FilamentLayout $layout,
        protected array $fields = [],
        protected int $width = 2,
        protected bool $card = true,
        protected array $titles = [],
        protected array $widths = [],
        protected array $schema = [],
    ) {
    }

    public function width(int $width = 1): self
    {
        $this->width = $width;

        return $this;
    }

    public function card(bool $card = true): self
    {
        $this->card = $card;

        return $this;
    }

    /**
     * @param  string|string[]  $titles
     */
    public function titles(string|array $titles = []): self
    {
        $this->titles = is_array($titles) ? $titles : [$titles];

        return $this;
    }

    /**
     * @param  int|int[]  $widths
     */
    public function widths(int|array $widths = []): self
    {
        $this->widths = is_array($widths) ? $widths : [$widths];

        return $this;
    }

    public function get(): FilamentLayout
    {
        $this->schema = $this->scanFields();

        // $this->layout->schema[] = Forms\Components\Group::make()
        //     ->schema($this->schema)
        //     ->columnSpan([
        //         'sm' => $this->width,
        //     ]);

        return $this->layout;
    }

    private function scanFields(): array
    {
        $list = [];

        foreach ($this->fields as $field) {
            if (is_array($field)) {
                // if only one field is passed, it is not an array
                if (1 === count($field)) {
                    $field = $field[0];
                }
            }

            $list[] = $field;
        }

        $schema = [];

        foreach ($list as $key => $group) {
            if (! is_array($group)) {
                $group = [$group];
            }

            if ($this->card) {
                $group = $this->setCard($group, $key);
            }

            $schema[] = Forms\Components\Group::make()
                ->schema($group)
                ->columnSpan([
                    'sm' => $this->width,
                ])
            ;
        }

        return $schema;
    }

    private function setCard(array $group, ?int $key = null): array
    {
        $title = null;

        if (array_key_exists($key, $this->titles)) {
            $title = $this->titles[$key];
        }

        // if (array_key_exists($key, $this->widths)) {
        //     $width = $this->width[$key];
        // }

        $fields = [];

        if ($title) {
            $fields[] = Forms\Components\Placeholder::make(Str::slug($title))
                ->label($title)
                ->columnSpan(2)
            ;
        }
        $fields = array_merge($fields, $group);

        $card = Forms\Components\Card::make()
            ->schema($fields)
        ;

        return [
            $card,
        ];
    }
}
