<?php

namespace Kiwilan\Steward\Filament\Config;

use Exception;
use Kiwilan\Steward\Filament\Config\FilamentBuilder\FilamentBuilderBlock;
use Kiwilan\Steward\Filament\Config\FilamentBuilder\FilamentBuilderContainer;
use Kiwilan\Steward\Filament\Config\FilamentBuilder\FilamentBuilderModule;

class FilamentBuilder
{
    public function __construct(
        protected string $builder,
        protected string $field = 'content',
        protected ?int $minItems = null,
        protected ?int $maxItems = null,
        protected mixed $instance = null,
    ) {}

    public static function make(string $builder): self
    {
        $instance = new $builder();

        if (! $instance instanceof FilamentBuilderModule) {
            throw new Exception('Builder must implement FilamentBuilderModule');
        }

        $builder = new self($builder);
        $builder->instance = $instance::make();

        return $builder;
    }

    public static function container(array $content): FilamentBuilderContainer
    {
        return FilamentBuilderContainer::make($content);
    }

    public static function block(array $fields): FilamentBuilderBlock
    {
        return FilamentBuilderBlock::make($fields);
    }

    public function get()
    {
        return FilamentBuilder::container($this->instance)
            ->field($this->field)
            ->minItems($this->minItems)
            ->maxItems($this->maxItems)
            ->get();
    }

    public function field(string $field = 'content'): self
    {
        $this->field = $field;

        return $this;
    }

    public function minItems(int $minItems): self
    {
        $this->minItems = $minItems;

        return $this;
    }

    public function maxItems(int $maxItems): self
    {
        $this->maxItems = $maxItems;

        return $this;
    }
}
