<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder;

use Filament\Forms;

class FilamentBuilderContainer
{
    public function __construct(
        protected array $content,
        protected string $field = 'content',
        protected ?int $minItems = null,
        protected ?int $maxItems = null,
        protected int $columnSpan = 2,
    ) {
    }

    public static function make(array $content): self
    {
        return new self($content);
    }

    public function get()
    {
        $container = Forms\Components\Builder::make($this->field)
            ->blocks([
                ...$this->content,
            ])
            ->collapsible()
            ->columnSpan($this->columnSpan);

        if ($this->minItems) {
            $container->minItems($this->minItems);
        }

        if ($this->maxItems) {
            $container->maxItems($this->maxItems);
        }

        return $container;
    }

    public function field(string $field = 'content'): self
    {
        $this->field = $field;

        return $this;
    }

    public function minItems(?int $minItems = null): self
    {
        $this->minItems = $minItems;

        return $this;
    }

    public function maxItems(?int $maxItems = null): self
    {
        $this->maxItems = $maxItems;

        return $this;
    }

    public function columnSpan(int $columnSpan = 2): self
    {
        $this->columnSpan = $columnSpan;

        return $this;
    }
}
