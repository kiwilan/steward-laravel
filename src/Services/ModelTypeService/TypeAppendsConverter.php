<?php

namespace Kiwilan\Steward\Services\ModelTypeService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * @property TypePropertyConverter[] $appendsTypes
 */
class TypeAppendsConverter
{
    protected function __construct(
        public Model $model,
        public array $appendsTypes = [],
    ) {
    }

    public static function make(Model $model): self
    {
        $converter = new TypeAppendsConverter($model);
        $reflector = new \ReflectionClass($converter->model);

        foreach ($reflector->getMethods() as $key => $method) {
            $name = $method->getName();
            $return = $method->getReturnType();

            if ($return instanceof ReflectionNamedType && $return->getName() === 'Illuminate\Database\Eloquent\Casts\Attribute') {
                $converter->appendsTypes[$name] = $converter->getAppendsMethods($name, $method);
            }

            if (str_starts_with($name, 'get') && str_ends_with($name, 'Attribute') && $name !== 'getAttribute') {
                $converter->appendsTypes[$name] = $converter->getAppendsMethods($name, $method);
            }
        }

        return $converter;
    }

    private function getAppendsMethods(string $name, ReflectionMethod $method): ?TypePropertyConverter
    {
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

        if ($field === 'mediable') {
            return $this->setMediables();
        } else {
            return TypePropertyConverter::create(
                model: $this->model,
                name: $field,
                type: $type,
                is_nullable: true,
            );
        }
    }

    private function setMediables(): ?TypePropertyConverter
    {
        if (method_exists($this->model, 'getMediablesListAttribute') && $this->model->getMediablesListAttribute()) {
            $mediable_object = '{';

            foreach ($this->model->getMediablesListAttribute() as $media) {
                $mediable_object .= " {$media}?: string, ";
            }

            $mediable_object .= '}';

            return TypePropertyConverter::create(
                model: $this->model,
                name: 'mediable',
                type: $mediable_object,
                override_type: true,
                is_nullable: true,
            );
        }

        return null;
    }
}
