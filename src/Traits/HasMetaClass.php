<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Meta Class, to give easy access to Model meta names.
 *
 * Example with `WikipediaItem`:
 * - `$meta_class`: `App\Models\WikipediaItem::class`
 * - `$meta_class_namespaced`: `App\Models\WikipediaItem`
 * - `$meta_class_name`: `WikipediaItem`
 * - `$meta_class_plural`: `WikipediaItems`
 * - `$meta_class_snake`: `wikipedia_item`
 * - `$meta_class_snake_plural`: `wikipedia_items`
 */
trait HasMetaClass
{
    /** Example: `WikipediaItem`: `App\Models\WikipediaItem::class` */
    public ?string $meta_class = null;
    /** Example: `WikipediaItem`: `App\Models\WikipediaItem` */
    public ?string $meta_class_namespaced = null;
    /** Example: `WikipediaItem`: `WikipediaItem` */
    public ?string $meta_class_name = null;
    /** Example: `WikipediaItem`: `WikipediaItems` */
    public ?string $meta_class_plural = null;
    /** Example: `WikipediaItem`: `wikipedia_item` */
    public ?string $meta_class_snake = null;
    /** Example: `WikipediaItem`: `wikipedia_items` */
    public ?string $meta_class_snake_plural = null;

    public function initializeHasMetaClass()
    {
        $reflection_class = new ReflectionClass($this);

        $this->meta_class_namespaced = $reflection_class->getName();
        $this->meta_class_name = $reflection_class->getShortName();
        $this->meta_class_plural = Str::plural($this->meta_class_name);

        $this->meta_class_snake = Str::snake($this->meta_class_name);
        $this->meta_class_snake_plural = Str::snake($this->meta_class_plural);

        $this->meta_class = $this->meta_class_namespaced.'::class';
    }
}
