<?php

namespace Kiwilan\Steward\Services\ModelTypeService;

use Illuminate\Database\Eloquent\Model;

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

        $appends = TypeAppendsConverter::make($model);
        $counts = TypePropertyConverter::make($counts);
        $appendsTypes = TypePropertyConverter::make($appends->appendsTypes);
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
