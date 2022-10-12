<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Meta Class, to give easy access to Model meta names.
 *
 * Example with `WikipediaItem`:
 *
 * @property string $meta_class              like `\App\Models\WikipediaItem::class`
 * @property string $meta_class_namespaced   like `\App\Models\WikipediaItem`
 * @property string $meta_class_name         like `WikipediaItem`
 * @property string $meta_class_name_plural  like `WikipediaItems`
 * @property string $meta_class_snake        like `wikipedia_item`
 * @property string $meta_class_snake_plural like `wikipedia_items`
 * @property string $meta_class_slug         like `wikipedia-item`
 * @property string $meta_class_slug_plural  like `wikipedia-items`
 * @property string $meta_first_char         like `w`
 */
trait HasMetaClass
{
    public function getMetaClassNamespacedAttribute(): string
    {
        $namespaced = $this->getInstance()->getName();

        return "\\{{$namespaced}}";
    }

    public function getMetaClassNameAttribute(): string
    {
        return $this->getInstance()->getShortName();
    }

    public function getMetaClassNamePluralAttribute(): string
    {
        return Str::plural($this->getInstance()->getShortName());
    }

    public function getMetaClassSnakeAttribute(): string
    {
        return Str::snake($this->getInstance()->getShortName());
    }

    public function getMetaClassSnakePluralAttribute(): string
    {
        return Str::snake(Str::plural($this->getInstance()->getShortName()));
    }

    public function getMetaClassSlugAttribute(): string
    {
        return Str::slug($this->getInstance()->getShortName());
    }

    public function getMetaClassSlugPluralAttribute(): string
    {
        return Str::slug(Str::plural($this->getInstance()->getShortName()));
    }

    public function getMetaFirstCharAttribute(): string
    {
        return Str::substr($this->getInstance()->getShortName(), 0, 1);
    }

    public function getMetaClassAttribute(): string
    {
        $meta_class = $this->getInstance()->getName();

        return "\\{{$meta_class}}::class";
    }

    private function getInstance()
    {
        return new ReflectionClass($this);
    }
}
