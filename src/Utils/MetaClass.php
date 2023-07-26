<?php

namespace Kiwilan\Steward\Utils;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Meta Class, to give easy access to Model meta names.
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

    /**
     * Like `App\Models\WikipediaItem::class`
     */
    public function class(): string
    {
        return $this->class;
    }

    /**
     * Like `App\Models\WikipediaItem`
     */
    public function classNamespaced(): string
    {
        return $this->classNamespaced;
    }

    /**
     * Like `WikipediaItem`
     */
    public function className(): string
    {
        return $this->className;
    }

    /**
     * Like `WikipediaItems`
     */
    public function classPlural(): string
    {
        return $this->classPlural;
    }

    /**
     * Like `wikipedia_item`
     */
    public function classSnake(): string
    {
        return $this->classSnake;
    }

    /**
     * Like `wikipedia_items`
     */
    public function classSnakePlural(): string
    {
        return $this->classSnakePlural;
    }

    /**
     * Like `wikipedia-item`
     */
    public function classSlug(): string
    {
        return $this->classSlug;
    }

    /**
     * Like `wikipedia-items`
     */
    public function classSlugPlural(): string
    {
        return $this->classSlugPlural;
    }

    /**
     * Like `w`
     */
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
