<?php

namespace Kiwilan\Steward\Services\ModelTypeService;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class TypeRelationConverter
{
    public function __construct(
        public string $name,
        public bool $is_array = false,
        public ?string $type = null,
        public ?TypePropertyConverter $typescript = null,
    ) {
    }

    /**
     * @return array<string,TypeRelationConverter>
     */
    public static function make(Model $model)
    {
        $reflect = new ReflectionClass($model);
        $relations = [];

        foreach ($reflect->getMethods() as $method) {
            $is_relation = str_contains($method->getReturnType(), 'Illuminate\Database\Eloquent\Relations');
            if (! $is_relation) {
                continue;
            }

            $relation = TypeRelationConverter::create([
                'name' => $method->getName(),
                'is_array' => str_contains($method->getReturnType(), 'Many'),
            ]);
            $return_line = $method->getEndLine() - 2;

            $lines = file($method->getFileName());
            $return_line_content = $lines[$return_line];

            $regex = '/\w+::class/';
            if (preg_match($regex, $return_line_content, $matches)) {
                $type = $matches[0];
                $type = str_replace('::class', '', $type);
                $relation->type = $type;
            }

            $relation->typescript = TypePropertyConverter::create(
                model: $model,
                name: $relation->name,
                type: $relation->type,
                is_nullable: true,
                is_array: $relation->is_array,
                override_type: true,
            );

            $relations[$relation->name] = $relation;
        }

        return $relations;
    }

    public static function create(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['is_array'] ?? false,
            $data['type'] ?? null,
        );
    }
}
