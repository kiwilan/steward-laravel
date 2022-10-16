<?php

namespace Kiwilan\Steward\Class;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Meta Class, to give easy access to Model meta names.
 *
 * @property string               $meta_class              like `App\Models\WikipediaItem::class`
 * @property string               $meta_class_namespaced   like `App\Models\WikipediaItem`
 * @property string               $meta_class_name         like `WikipediaItem`
 * @property string               $meta_class_plural       like `WikipediaItems`
 * @property string               $meta_class_snake        like `wikipedia_item`
 * @property string               $meta_class_snake_plural like `wikipedia_items`
 * @property string               $meta_class_slug         like `wikipedia-item`
 * @property string               $meta_class_slug_plural  like `wikipedia-items`
 * @property string               $meta_first_char         like `w`
 * @property array<string,string> $meta_traits
 */
class MetaClass
{
    public function __construct(
        public string $meta_class,
        public ?string $meta_class_namespaced = null,
        public ?string $meta_class_name = null,
        public ?string $meta_class_plural = null,
        public ?string $meta_class_snake = null,
        public ?string $meta_class_snake_plural = null,
        public ?string $meta_class_slug = null,
        public ?string $meta_class_slug_plural = null,
        public ?string $meta_first_char = null,
        public array $meta_traits = [],
    ) {
    }

    public static function make(string $class): self
    {
        $metadata = new MetaClass($class);

        $instance = new $class();
        $reflection_class = new ReflectionClass($instance);

        $metadata->meta_class_namespaced = $reflection_class->getName();
        $metadata->meta_class_name = $reflection_class->getShortName();
        $metadata->meta_class_plural = Str::plural($metadata->meta_class_name);

        $metadata->meta_class_snake = Str::snake($metadata->meta_class_name);
        $metadata->meta_class_snake_plural = Str::snake($metadata->meta_class_plural);

        $metadata->meta_class_slug = Str::slug($metadata->meta_class_name);
        $metadata->meta_class_slug_plural = Str::slug($metadata->meta_class_plural);

        $metadata->meta_first_char = strtolower(substr($metadata->meta_class_name, 0, 1));
        $metadata->meta_traits = class_uses_recursive($instance);

        return $metadata;
    }

    /**
     * Check if current instance has a given trait.
     *
     * @param string $trait like `Publishable::class`
     */
    public function useTrait(string $trait): bool
    {
        return in_array($trait, $this->meta_traits);
    }
}
