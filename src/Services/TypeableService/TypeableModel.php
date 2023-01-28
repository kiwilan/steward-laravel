<?php

namespace Kiwilan\Steward\Services\TypeableService;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Kiwilan\Steward\Services\TypeableService\Utils\TypeableDbColumn;
use Kiwilan\Steward\Services\TypeableService\Utils\TypeableTeam;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * @property string $table
 * @property TypeableClass|null $class
 * @property TypeableProperty[] $columns
 * @property TypeableRelation[] $relations
 * @property string[] $appends
 * @property TypeableProperty[] $properties
 */
class TypeableModel
{
    public function __construct(
        public ?string $table = null,
        public ?string $name = null,
        public ?TypeableClass $class = null,
        /** @var TypeableProperty[] */
        public array $columns = [],
        /** @var TypeableRelation[] */
        public array $relations = [],
        public array $appends = [],
        public ?string $mediable = null,
        /** @var TypeableProperty[] */
        public array $properties = [],
        public ?string $tsString = null,
        public ?string $phpString = null,
    ) {
    }

    public static function make(TypeableClass $class): self
    {
        $parser = new self(table: $class->table, name: $class->name, class: $class);

        /** @var TypeableDbColumn[] */
        $dbColumns = DB::select(DB::raw("SHOW COLUMNS FROM {$class->table}"));

        foreach ($dbColumns as $column) {
            $column = TypeableDbColumn::make($column);
            $parser->columns[$column->Field] = TypeableProperty::make($class->table, $column);
        }

        $parser->setAppends();
        $parser->setRelations();

        if ($parser->class->name === 'User') {
            $parser->setFakeTeam();
        }
        $parser->setProperties();

        foreach ($parser->properties as $field => $property) {
            $parser->properties[$field] = $property->convertCastType($field, $parser->class->casts);
            $parser->properties[$field] = $property->convertDateType($parser->class->dates);
            $parser->properties[$field] = $property->setAdvancedType();
            $parser->properties[$field] = $property->setTsType();
        }

        $parser->tsString = $parser->convertToTs();
        $parser->phpString = $parser->convertToPhp();

        return $parser;
    }

    /**
     * @param  TypeableProperty[]  $properties
     */
    public static function fake(TypeableClass $class, array $properties): self
    {
        $model = new self(table: $class->table, name: $class->name, class: $class, columns: $properties);
        $model->setProperties();

        foreach ($model->properties as $field => $property) {
            $model->properties[$field] = $property->setTsType();
        }
        $model->tsString = $model->convertToTs();

        return $model;
    }

    private function convertToPhp(): string
    {
        $php[] = '<?php';
        $php[] = '';
        $php[] = 'namespace App\Types;';
        $php[] = '';
        $php[] = '// This file is auto generated by GenerateTypeCommand.';
        $php[] = "class {$this->class->name}";
        $php[] = '{';

        foreach ($this->properties as $property) {
            $php[] = $property->phpString;
        }

        $php[] = '};';
        $php[] = '';

        return implode(PHP_EOL, $php);
    }

    private function convertToTs(): string
    {
        $typescript[] = "  export type {$this->class->name} = {";

        foreach ($this->properties as $property) {
            $typescript[] = $property->tsString;
        }

        $typescript[] = '  };';

        return implode(PHP_EOL, $typescript);
    }

    private function setFakeTeam()
    {
        $this->properties = TypeableTeam::setUserFakeTeam();
    }

    private function setRelations()
    {
        $this->relations = TypeableRelation::make($this);
    }

    private function setProperties()
    {
        foreach ($this->columns as $column) {
            if (! in_array($column->name, $this->class->hidden)) {
                $this->properties[$column->name] = $column;
            }
        }

        foreach ($this->appends as $field => $type) {
            $this->properties[$field] = TypeableProperty::make(
                table: $this->table,
                dbColumn: new TypeableDbColumn($field, $type),
                isAppend: true,
            );
        }

        foreach ($this->relations as $field => $relation) {
            $this->properties[$field] = TypeableProperty::make(
                table: $this->class->table,
                dbColumn: new TypeableDbColumn($field, $relation->type),
                overrideTsType: true,
                isRelation: true,
                isArray: $relation->isArray,
            );
        }

        if ($this->mediable) {
            $this->properties['mediable'] = TypeableProperty::make(
                table: $this->table,
                dbColumn: new TypeableDbColumn('mediable', $this->mediable),
                overrideTsType: true,
            );
        }
    }

    private function setAppends()
    {
        foreach ($this->class->reflector->getMethods() as $key => $method) {
            $name = $method->getName();
            $return = $method->getReturnType();

            if ($name !== 'getMediableAttribute') {
                if ($return instanceof ReflectionNamedType && $return->getName() === 'Illuminate\Database\Eloquent\Casts\Attribute') {
                    $this->setAppendMethod($name, $method);
                }

                if (str_starts_with($name, 'get') && str_ends_with($name, 'Attribute') && $name !== 'getAttribute') {
                    $this->setAppendMethod($name, $method);
                }
            } else {
                $this->setMediable();
            }
        }
    }

    private function setAppendMethod(string $name, ReflectionMethod $method)
    {
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

        if ($type instanceof ReflectionNamedType) {
            $type = $type->getName();
        }

        $this->appends[$field] = $type;
    }

    private function setMediable(): self
    {
        $reflector = $this->class->reflector->getMethods();
        $names = array_map(fn ($method) => $method->getName(), $reflector);

        $mediable_object = null;

        if (in_array('getMediablesListAttribute', $names) && method_exists($this->class->model, 'getMediablesListAttribute')) {
            $mediable_object = '{';

            foreach ($this->class->model->getMediablesListAttribute() as $media) {
                $mediable_object .= " {$media}?: string, ";
            }
            $mediable_object .= '}';
        }

        $this->mediable = $mediable_object;

        return $this;
    }
}
