<?php

namespace Kiwilan\Steward\Services\ClassParser;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * MetaClassItem, to give easy access to Model meta names.
 */
class MetaClassItem
{
    protected function __construct(
        protected string $classString,
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

    /**
     * Create a new MetaClass instance.
     *
     * @param  string  $classString like `WikipediaItem::class`
     */
    public static function make(string $classString, ReflectionClass $reflect = null): self
    {
        $self = new self($classString);

        $instance = new $classString();

        if (! $reflect) {
            $reflect = new ReflectionClass($instance);
        }

        $self->classNamespaced = $reflect->getName();
        $self->className = $reflect->getShortName();
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
    public function getClassString(): string
    {
        return $this->classString;
    }

    /**
     * Like `App\Models\WikipediaItem`
     */
    public function getClassNamespaced(): string
    {
        return $this->classNamespaced;
    }

    /**
     * Like `WikipediaItem`
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Like `WikipediaItems`
     */
    public function getClassPlural(): string
    {
        return $this->classPlural;
    }

    /**
     * Like `wikipedia_item`
     */
    public function getClassSnake(): string
    {
        return $this->classSnake;
    }

    /**
     * Like `wikipedia_items`
     */
    public function getClassSnakePlural(): string
    {
        return $this->classSnakePlural;
    }

    /**
     * Like `wikipedia-item`
     */
    public function getClassSlug(): string
    {
        return $this->classSlug;
    }

    /**
     * Like `wikipedia-items`
     */
    public function getClassSlugPlural(): string
    {
        return $this->classSlugPlural;
    }

    /**
     * Like `w`
     */
    public function getFirstChar(): string
    {
        return $this->firstChar;
    }

    /**
     * @return array<string,string>
     */
    public function getTraits(): array
    {
        return $this->traits;
    }
}
