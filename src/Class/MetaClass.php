<?php

namespace Kiwilan\Steward\Class;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Meta Class, to give easy access to Model meta names.
 *
 * - `class()` `string` like `App\Models\WikipediaItem::class`
 * - `classNamespaced()` `string` like `App\Models\WikipediaItem`
 * - `className()` `string` like `WikipediaItem`
 * - `classPlural()` `string` like `WikipediaItems`
 * - `classSnake()` `string` like `wikipedia_item`
 * - `classSnakePlural()` `string` like `wikipedia_items`
 * - `classSlug()` `string` like `wikipedia-item`
 * - `classSlugPlural()` `string` like `wikipedia-items`
 * - `firstChar()` `string` like `w`
 * - `traits()` `array<string,string>`
 */
class MetaClass
{
    protected function __construct(
        protected string $class,
        protected ?string $classNamespaced = null,
        protected ?string $className = null,
        protected ?string $classPlural = null,
        protected ?string $classSnake = null,
        protected ?string $classSnakePlural = null,
        protected ?string $classSlug = null,
        protected ?string $classSlugPlural = null,
        protected ?string $firstChar = null,
        protected array $traits = [],
    ) {
    }

    public static function make(string $class): self
    {
        $self = new self($class);

        $instance = new $class();
        $reflection_class = new ReflectionClass($instance);

        $self->classNamespaced = $reflection_class->getName();
        $self->className = $reflection_class->getShortName();
        $self->classPlural = Str::plural($self->className);

        $self->classSnake = Str::snake($self->className);
        $self->classSnakePlural = Str::snake($self->classPlural);

        $self->classSlug = Str::slug($self->className);
        $self->classSlugPlural = Str::slug($self->classPlural);

        $self->firstChar = strtolower(substr($self->className, 0, 1));
        $self->traits = class_uses_recursive($instance);

        return $self;
    }

    /**
     * Check if current instance has a given trait.
     *
     * @param  string  $trait like `Publishable::class`
     */
    public function useTrait(string $trait): bool
    {
        return in_array($trait, $this->traits);
    }

    public function class(): string
    {
        return $this->class;
    }

    public function classNamespaced(): string
    {
        return $this->classNamespaced;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function classPlural(): string
    {
        return $this->classPlural;
    }

    public function classSnake(): string
    {
        return $this->classSnake;
    }

    public function classSnakePlural(): string
    {
        return $this->classSnakePlural;
    }

    public function classSlug(): string
    {
        return $this->classSlug;
    }

    public function classSlugPlural(): string
    {
        return $this->classSlugPlural;
    }

    public function firstChar(): string
    {
        return $this->firstChar;
    }

    /**
     * @return array<string,string>
     */
    public function traits(): array
    {
        return $this->traits;
    }
}
