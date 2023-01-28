<?php

namespace Kiwilan\Steward\Services\TypeableService;

use Kiwilan\Steward\Services\TypeableService\Utils\TypeableDbColumn;
use Kiwilan\Steward\Services\TypeableService\Utils\TypeableTypes;

/**
 * @property string[] $enum
 */
class TypeableProperty
{
    public function __construct(
        public string $table,
        public string $name,
        public ?TypeableDbColumn $dbColumn = null,
        public bool $isPrimary = false,
        public bool $isNullable = false,
        public ?string $phpType = null,
        public ?string $cast = null,
        public ?string $externalType = null,
        public array $enum = [],
        public bool $isExternal = false,
        public bool $isEnum = false,
        public bool $overrideTsType = false,
        public ?string $tsType = null,
        public ?string $tsString = null,
    ) {
    }

    public static function make(string $table, TypeableDbColumn $dbColumn, bool $overrideTsType = false): self
    {
        $property = new self(
            table: $table,
            name: $dbColumn->Field,
            dbColumn: $dbColumn,
            isPrimary: $dbColumn->Key === 'PRI',
            isNullable: $dbColumn->Null === 'YES',
            overrideTsType: $overrideTsType,
        );

        if ($property->overrideTsType) {
            $property->phpType = $property->dbColumn->Type;
        } else {
            $property->phpType = TypeableTypes::phpType($dbColumn->Type);
        }

        return $property;
    }

    public function setAdvancedType(): self
    {
        $type = TypeableTypes::docTypeToTsType($this);

        if ($type && ! $this->overrideTsType) {
            $this->phpType = $type;
            $this->tsType = $type;
            $this->overrideTsType = true;
        }

        return $this;
    }

    public function setTsType(): self
    {
        if (str_contains($this->phpType, '?')) {
            $this->phpType = str_replace('?', '', $this->phpType);
        }

        $this->tsType = TypeableTypes::phpToTs($this->phpType);

        if ($this->overrideTsType) {
            $this->tsType = $this->phpType;
        }
        $isNullable = $this->isNullable ? '?' : '';

        $this->tsString = "    {$this->name}{$isNullable}: {$this->tsType};";

        return $this;
    }

    /**
     * @param  string[]  $dates
     */
    public function convertDateType(array $dates): self
    {
        if (in_array($this->name, $dates)) {
            $this->phpType = 'DateTime';
        }

        return $this;
    }

    /**
     * @param  string[]  $casts
     */
    public function convertCastType(string $field, array $casts): self
    {
        if (! isset($casts[$field])) {
            return $this;
        }

        $this->cast = $casts[$field];
        $castType = TypeableTypes::castToPhpType($this->cast);

        $this->phpType = $castType;

        if (str_contains($this->cast, '\\')) {
            $this->isExternal = true;
            $this->externalType = $castType;
            $this->setEnum();
        }

        return $this;
    }

    private function setEnum(): self
    {
        $reflector = new \ReflectionClass($this->cast);
        $this->isEnum = TypeableTypes::isEnum($reflector);

        if ($this->isEnum) {
            $this->enum = TypeableTypes::setEnum($reflector);
            $this->overrideTsType = true;
            $this->phpType = TypeableTypes::phpEnumToTsType($this->enum);
        }

        return $this;
    }
}
