<?php

namespace Kiwilan\Steward\Services\ModelTypeService;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string[] $enum_types
 */
class TypePropertyConverter
{
    protected function __construct(
        public Model $model,
        public string $name,
        public string $php_type = 'string',
        public bool $is_nullable = false,
        public bool $is_array = false,
        public bool $override_type = false,
        public string $ts_type = 'any',
        public bool $type_is_external = false,
        public bool $type_is_enum = false,
        public array $enum_types = [],
        public ?string $typescript = null,
    ) {
    }

    public static function create(
        Model $model,
        string $name,
        string $type = 'any',
        bool $is_nullable = false,
        bool $is_array = false,
        bool $override_type = false,
    ): self {
        $converter = new TypePropertyConverter($model, $name, $type, $is_nullable, $is_array, $override_type);

        $enum_types = [];
        $converter->type_is_enum = false;
        $converter->type_is_external = str_contains($type, '\\');

        if ($converter->type_is_external) {
            $reflector = new \ReflectionClass($type);
            $converter->type_is_enum = in_array('UnitEnum', $reflector->getInterfaceNames());

            if ($converter->type_is_enum) {
                foreach ($reflector->getConstants() as $name => $enum) {
                    $enum_types[$name] = is_string($enum) ? "'{$enum}'" : $enum->value;
                }
            }
        }

        $converter->enum_types = $enum_types;
        $converter->phpTypeToTsType();

        return $converter;
    }

    private function convertPhpType(string $php_type): string
    {
        if (str_contains($php_type, '?')) {
            $php_type = str_replace('?', '', $php_type);
        }

        return match ($php_type) {
            'string' => 'string',
            'int' => 'number',
            'integer' => 'number',
            'float' => 'number',
            'bool' => 'boolean',
            'boolean' => 'boolean',
            'array' => 'any[]',
            'object' => 'any',
            'mixed' => 'any',
            'null' => 'null',
            'DateTime' => 'Date',
            'DateTimeInterface' => 'Date',
            'Carbon' => 'Date',
            'Model' => 'any',
            default => 'any',
        };
    }

    private function phpTypeToTsType()
    {
        if (str_contains($this->php_type, '?')) {
            $this->is_nullable = true;
            $this->php_type = str_replace('?', '', $this->php_type);
        }

        $this->ts_type = $this->convertPhpType($this->php_type);

        if ($this->override_type) {
            $this->ts_type = $this->php_type;

            if ($this->type_is_external) {
                $name = class_basename($this->php_type);
                $this->ts_type = $name;
            } else {
                $this->php_type = str_contains($this->php_type, 'date') ? 'DateTime' : $this->php_type;
                $this->ts_type = match ($this->php_type) {
                    'DateTime' => 'Date',
                    'int' => 'number',
                    'integer' => 'number',
                    default => $this->php_type,
                };
            }
        }

        if ($this->is_array) {
            $this->ts_type .= '[]';
        }

        if ($this->is_nullable) {
            $this->ts_type = "{$this->ts_type} | undefined";
        }

        if ($this->ts_type === 'any' || $this->ts_type === 'any | undefined') {
            $this->ts_type = $this->parseAdvancedTypes();
        }

        return $this;
    }

    private function parseAdvancedTypes(): string
    {
        $ts_type = 'any';

        dump($this->php_type);

        if (str_contains($this->php_type, 'array')) {
            $type = null;
            $regex = '/<[^>]*>/';

            if (preg_match($regex, $this->php_type, $matches)) {
                $type = $matches[0] ?? null;
            }

            $type = str_replace('<', '', $type);
            $type = str_replace('>', '', $type);
            $type = str_replace(' ', '', $type);

            if (str_contains($type, ',')) {
                $types = explode(',', $type);
                $ts_type = "{$this->convertPhpType($types[0])}[]";
            } else {
                $ts_type = "{$this->convertPhpType($type)}[]";
            }
        }

        if (str_contains($this->php_type, '[]')) {
            $type = str_replace('[]', '', $this->php_type);
            $ts_type = "{$this->convertPhpType($type)}[]";
        }

        return $ts_type;
    }

    /**
     * @param  TypePropertyConverter[]  $converters
     * @return TypePropertyConverter[]
     */
    public static function make(array $converters): array
    {
        $break = PHP_EOL;

        $types = [];

        foreach ($converters as $converter) {
            $converter->typescript = "    {$converter->name}: {$converter->ts_type};{$break}";
            $types[$converter->name] = $converter;
        }

        return $types;
    }
}
