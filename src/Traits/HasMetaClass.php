<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Meta Class, to give easy access to Model meta names.
 *
 * Example with `WikipediaItem`:
 *
 * @property string $meta_class like `App\Models\WikipediaItem::class`
 * @property string $meta_class_namespaced like `App\Models\WikipediaItem`
 * @property string $meta_class_name like `WikipediaItem`
 * @property string $meta_class_plural like `WikipediaItems`
 * @property string $meta_class_snake like `wikipedia_item`
 * @property string $meta_class_snake_plural like `wikipedia_items`
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

    /** Example: `WikipediaItem`: `wikipedia-item` */
    public ?string $class_kebab = null;

    /** Example: `WikipediaItem`: `wikipedia-items` */
    public ?string $class_kebab_plural = null;

    /** Example: `WikipediaItem`: `w` */
    public ?string $first_char = null;

    public function initializeHasMetaClass()
    {
        $reflection_class = new ReflectionClass($this);

        $this->meta_class_namespaced = $reflection_class->getName();
        $this->meta_class_name = $reflection_class->getShortName();
        $this->meta_class_plural = Str::plural($this->meta_class_name);

        $this->meta_class_snake = Str::snake($this->meta_class_name);
        $this->meta_class_snake_plural = Str::snake($this->meta_class_plural);

        $this->class_kebab = Str::kebab($this->class_name);
        $this->class_kebab_plural = Str::kebab($this->class_plural);

        $this->meta_class = $this->meta_class_namespaced.'::class';

        $this->first_char = strtolower(substr($this->meta_class_name, 0, 1));
    }
}
