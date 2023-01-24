<?php

namespace Kiwilan\Steward\Services\ModelTypeService;

use Doctrine\DBAL\Types\Types;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

class TypeColumnConverter
{
    public function __construct(
        public string $Field,
        public string $Type,
        public string $Null,
        public string $Key,
        public ?string $Default,
        public string $Extra,
        public bool $isPrimary = false,
        public bool $isNullable = false,
        public ?Model $model = null,
        public ?TypePropertyConverter $typescript = null,
    ) {
    }

    /**
     * @return TypeColumnConverter[]
     */
    public static function tableToColumns(Model $model): array
    {
        $table = $model->getTable();

        if (! Schema::hasTable($table)) {
            throw new \Exception("Table $table does not exist");
        }

        $columns = DB::select(DB::raw("SHOW COLUMNS FROM $table"));

        return array_map(fn ($column) => self::create($column, $model), $columns);
    }

    private static function create(stdClass $data, Model $model): self
    {
        $column = new self(
            $data->Field,
            $data->Type,
            $data->Null,
            $data->Key,
            $data->Default,
            $data->Extra,
            //
            $data->Key === 'PRI',
            $data->Null === 'YES',
        );
        $column->model = $model;

        $type = $column->mapDatabaseTypeToPhpType($column);
        $casts = $model->getCasts();
        $dates = $model->getDates();

        $is_date = in_array($column->Field, $dates);

        if ($is_date) {
            $type = 'DateTime';
        }
        $is_cast = array_key_exists($column->Field, $casts);

        $column->typescript = TypePropertyConverter::create(
            model: $model,
            name: $column->Field,
            type: $casts[$column->Field] ?? $type,
            is_nullable: $column->isNullable,
            is_array: $type === 'array' || $type === 'object' || $type === 'mixed' || $type === 'json',
            override_type: $is_cast,
        );

        return $column;
    }

    private function mapDatabaseTypeToPhpType(TypeColumnConverter $column): string
    {
        $types = [
            'double' => Types::FLOAT,
            'numeric' => Types::FLOAT,
            'decimal' => Types::FLOAT,
            'varchar' => Types::STRING,
            'char' => Types::STRING,
            'binary' => Types::STRING,
            'varbinary' => Types::STRING,
            'tinyblob' => Types::STRING,
            'blob' => Types::STRING,
            'mediumblob' => Types::STRING,
            'longblob' => Types::STRING,
            'tinytext' => Types::STRING,
            'text' => Types::STRING,
            'mediumtext' => Types::STRING,
            'longtext' => Types::STRING,
            'enum' => Types::STRING,
            'bigint' => 'int',
            'int' => 'int',
            'tinyint' => Types::BOOLEAN,
            'id' => 'int',
            'date' => Types::STRING,
            'time' => Types::STRING,
            'datetime' => Types::STRING,
            'year' => Types::STRING,
            'timestamp' => Types::STRING,
            'json' => Types::STRING,
        ];

        $type = explode(' ', preg_replace('/\s*\([^)]*\)/', '', $column->Type))[0];

        return $types[$type];
    }
}
