<?php

namespace Kiwilan\Steward\Queries\Options;

use Illuminate\Support\Str;
use ReflectionClass;

class ClassMetadata
{
    public function __construct(
        public ?string $class = null,
        public ?string $class_namespaced = null,
        public ?string $class_name = null,
        public ?string $class_plural = null,
        public ?string $class_snake = null,
        public ?string $class_plural_snake = null,
    ) {
    }

    public static function create(string $class): self
    {
        $metadata = new ClassMetadata($class);

        $instance = new $class();
        $reflection_class = new ReflectionClass($instance);

        $metadata->class_namespaced = $reflection_class->getName();
        $metadata->class_name = $reflection_class->getShortName();
        $metadata->class_plural = Str::plural($metadata->class_name);

        $metadata->class_snake = Str::snake($metadata->class_name);
        $metadata->class_plural_snake = Str::snake($metadata->class_plural);

        return $metadata;
    }
}
