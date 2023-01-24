<?php

namespace Kiwilan\Steward\Services\ModelTypeService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TypeModelConverter
{
    protected function __construct(
        public Model $model,
        public string $name,
        public ?string $typescript = null,
    ) {
    }

    /**
     * @param  Model  $model
     * @param  TypePropertyConverter[]  $types
     */
    public static function make(Model $model, array $types): self
    {
        $reflector = new \ReflectionClass($model);
        $converter = new TypeModelConverter($model, $reflector->getShortName());

        $hidden = $model->getHidden();

        $appendsTypes = [];
        $appendsMethods = [];

        foreach ($reflector->getMethods() as $key => $method) {
            $name = $method->getName();

            if (! str_starts_with($name, 'get') || ! str_contains($name, 'Attribute')) {
                continue;
            }

            if (! $method->getReturnType()) {
                continue;
            }

            $appendsMethods[$name] = $method;

            $field = str_replace('Attribute', '', str_replace('get', '', $name));
            $field = Str::snake($field);
            $doc = $method->getDocComment();
            $return = null;

            $regex = '/(?m)@return *\K(?>(\S+) *)??(\S+)$/';

            if (preg_match($regex, $doc, $matches)) {
                $return = $matches[0] ?? null;
            }

            $type = $method->getReturnType();

            if ($return) {
                $type = $return;
            }

            $is_mediable = method_exists($model, 'getMediablesListAttribute');

            if ($field === 'mediable' && $is_mediable) {
                $mediable_object = '{';

                foreach ($model->getMediablesListAttribute() as $media) {
                    $mediable_object .= " {$media}?: string, ";
                }
                $mediable_object .= '}';
                $mediable_object .= ' | undefined';

                $appendsTypes[$name] = TypePropertyConverter::create(
                    model: $model,
                    name: $field,
                    type: $mediable_object,
                    override_type: true,
                );
            } else {
                $appendsTypes[$name] = TypePropertyConverter::create(
                    model: $model,
                    name: $field,
                    type: $type,
                );
            }
        }

        $counts = [];

        foreach ($types as $name => $type) {
            if ($type->is_array) {
                $counts[$name] = TypePropertyConverter::create(
                    model: $model,
                    name: "{$type->name}_count",
                    type: 'int',
                    is_nullable: true,
                );
            }
        }

        $counts = TypePropertyConverter::make($counts);
        $appendsTypes = TypePropertyConverter::make($appendsTypes);
        $types = array_merge($types, $appendsTypes, $counts);

        $typescript = [];
        $enums = [];

        $typescript[] = "  export type {$converter->name} = {".PHP_EOL;

        foreach ($types as $name => $type) {
            if (! in_array($name, $hidden)) {
                if ($type->type_is_enum) {
                    $enums[class_basename($type->php_type)] = $type->enum_types;
                }
                $typescript[] = "{$type->typescript}";
            }
        }
        $typescript[] = '  };'.PHP_EOL;

        $converter->typescript = implode('', $typescript);

        if (! empty($enums)) {
            $enum_types = [];

            foreach ($enums as $name => $enum) {
                $enum = array_map(fn ($value) => "'{$value}'", $enum);
                $list = implode(' | ', $enum);
                $enum_types[] = "  export type {$name} = {$list};";
            }
            $converter->typescript .= implode(PHP_EOL, $enum_types).PHP_EOL;
        }

        return $converter;
    }
}
