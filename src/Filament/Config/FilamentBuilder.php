<?php

namespace Kiwilan\Steward\Filament\Config;

use Exception;
use Kiwilan\Steward\Filament\Config\FilamentBuilder\HelperBuilder;
use Kiwilan\Steward\Filament\Config\FilamentBuilder\IFilamentBuilder;

class FilamentBuilder
{
    public function __construct(
        protected string $builder,
        protected string $field = 'content',
        protected ?int $minItems = null,
        protected ?int $maxItems = null,
        protected mixed $instance = null,
    ) {
    }

    public static function make(string $builder): self
    {
        $instance = new $builder();
        if (! $instance instanceof IFilamentBuilder) {
            throw new Exception('Builder must implement IFilamentBuilder');
        }

        $builder = new FilamentBuilder($builder);
        $builder->instance = $instance::make();

        return $builder;
    }

    public function get()
    {
        return HelperBuilder::container(
            content: $this->instance,
            field: $this->field,
            minItems: $this->minItems,
            maxItems: $this->maxItems,
        );
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
