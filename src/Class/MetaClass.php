<?php

namespace Kiwilan\Steward\Class;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Meta Class, to give easy access to Model meta names.
 *
 * @property string $meta_class like `App\Models\WikipediaItem::class`
 * @property string $meta_class_namespaced like `App\Models\WikipediaItem`
 * @property string $meta_class_name like `WikipediaItem`
 * @property string $meta_class_plural like `WikipediaItems`
 * @property string $meta_class_snake like `wikipedia_item`
 * @property string $meta_class_snake_plural like `wikipedia_items`
 */
class MetaClass
{
    public function __construct(
        public ?string $class = null,
        public ?string $class_namespaced = null,
        public ?string $class_name = null,
        public ?string $class_plural = null,
        public ?string $class_snake = null,
        public ?string $class_snake_plural = null,
    ) {
    }

    public static function make(string $class): self
    {
        $metadata = new MetaClass($class);

        $instance = new $class();
        $reflection_class = new ReflectionClass($instance);

        $metadata->class_namespaced = $reflection_class->getName();
        $metadata->class_name = $reflection_class->getShortName();
        $metadata->class_plural = Str::plural($metadata->class_name);

        $metadata->class_snake = Str::snake($metadata->class_name);
        $metadata->class_snake_plural = Str::snake($metadata->class_plural);

        return $metadata;
    }
}
