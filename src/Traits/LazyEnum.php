<?php

namespace Kiwilan\Steward\Traits;

use BackedEnum;
use Closure;
use Exception;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionEnum;

trait LazyEnum
{
    public static function tryFromCase(string $caseName): ?self
    {
        $rc = new ReflectionEnum(self::class);

        return $rc->hasCase($caseName) ? $rc->getConstant($caseName) : null;
    }

    /**
     * @throws Exception
     */
    public static function fromCase(string $caseName): self
    {
        return self::tryFrom($caseName) ?? throw new Exception('Enum '.$caseName.' not found in '.self::class);
    }

    public static function toList(): array
    {
        $list = [];

        foreach (self::cases() as $enum) {
            if ($enum instanceof BackedEnum) {
                $list[$enum->value] = $enum->value;
            } else { // @phpstan-ignore-line
                $list[$enum] = $enum;
            }
        }

        return $list;
    }

    public static function toDatabase(): array
    {
        return self::toList();
    }

    public static function toNames(): array
    {
        $array = [];

        foreach (static::cases() as $definition) {
            $array[$definition->name] = $definition->name;
        }

        return $array;
    }

    public static function toValues(): array
    {
        $array = [];

        foreach (static::cases() as $definition) {
            $array[$definition->name] = $definition->value;
        }

        return $array;
    }

    public static function getLocaleBaseName(): string
    {
        $class = new ReflectionClass(static::class);
        $namespace = 'App\\Enums' === $class->getNamespaceName() ? '' : 'steward::';
        $class = $class->getShortName();
        $class_slug = Str::kebab($class);
        $class_slug = str_replace('-enum', '', $class_slug);
        $class_slug = str_replace('-', '_', $class_slug);

        $locale = "{$namespace}enums.{$class_slug}.";

        return $locale;
    }

    /**
     * Get Enum list [case] => [locale].
     */
    public static function toArray(): array
    {
        $array = [];
        $base = static::getLocaleBaseName();

        foreach (static::cases() as $definition) {
            $locale = "{$base}{$definition->value}";
            $array[$definition->name] = Lang::has($locale)
                ? __($locale)
                : ucfirst($definition->value);
        }
        asort($array);

        return $array;
    }

    public static function toString(): string
    {
        $list = self::toArray();

        return implode(', ', $list);
    }

    public function locale(bool $lower = false): string
    {
        $base = static::getLocaleBaseName();
        $locale = "{$base}{$this->value}";
        $locale = Lang::has($locale) ? $locale : ucfirst($this->value);

        return $lower ? strtolower(__($locale)) : __($locale);
    }

    public function equals(...$others): bool
    {
        foreach ($others as $other) {
            if (
                get_class($this) === get_class($other)
                && $this->value === $other->value
            ) {
                return true;
            }
        }

        return false;
    }

    public function i18n(): string
    {
        $class = new ReflectionClass(static::class);
        $class = $class->getShortName();

        return Lang::has("enum.enums.{$class}.{$this->name}")
            ? __("enum.enums.{$class}.{$this->name}")
            : $this->name;
    }

    protected static function values(): Closure
    {
        return fn (string $name) => mb_strtolower($name);
    }
}
