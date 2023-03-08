<?php

namespace Kiwilan\Steward\Services\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kiwilan\Steward\Services\Class\ClassItem;
use Kiwilan\Steward\Services\FactoryService;

class FactoryData
{
    public function __construct(
        public FactoryService $factory,
        protected ?string $class = null,
        protected ?ClassItem $item = null,
        protected ?Model $model = null,
        protected array $transFields = [],
    ) {
    }

    public function get(string $class): bool
    {
        $this->class = $class;
        $this->item = ClassItem::make($class);

        if (! $this->item->isModel()) {
            throw new \Exception("{$class} must be an instance of Illuminate\Database\Eloquent\Model");
        }

        $this->model = $this->item->model();

        $name = $this->model->getTable();
        $name = Str::replace('_', '-', $name);

        if ($this->item->useTrait('Laravel\Scout\Searchable')) {
            return $class::withoutSyncingToSearch(function () {
                return $this->parseJson();
            });
        }

        return $this->parseJson();
    }

    private function parseJson(): bool
    {
        // Check if model has a table property
        $name = $this->item->model()->getTable();
        $name = Str::replace('_', '-', $name);

        $this->transFields = [];

        // Check if model has a translatable property
        if ($this->item->propertyExists('translatable')) {
            // @phpstan-ignore-next-line
            $this->transFields = $this->item->model()->translatable;
        }

        $pathJson = database_path("seeders/data/{$name}.json");

        if (! File::exists($pathJson)) {
            echo "No JSON detected on `{$pathJson}`";

            return false;
        }

        $jsonData = json_decode(File::get($pathJson));

        foreach ($jsonData as $entity) {
            $this->parseEntity($entity);
        }

        return true;
    }

    private function parseEntity(mixed $entity): void
    {
        $data = (array) $entity;
        $data_entity = $data;
        unset($data_entity['foreign']);

        $trans_values = [];

        foreach ($this->transFields as $field) {
            unset($data_entity[$field]);
            $trans_values[$field] = (array) $data[$field];
        }

        $data_entity = [
            ...$data_entity,
            ...$trans_values,
        ];

        $factory = FactoryService::make();

        /** @var Model */
        $created_model = $this->class::create($data_entity);

        if ($created_model->isFillable('picture')) {
            $created_model = $factory->mediaLocal()->setMedia($created_model);
        }

        if (array_key_exists('foreign', $data)) {
            $foreign = (array) $data['foreign'];

            foreach ($foreign as $relation => $value) {
                $foreign_model = "\\App\\Models\\{$value->model}";
                $foreign_key = $value->data;

                $foreign_entity = $foreign_model::whereSlug($foreign_key)->first();
                $created_model->{$relation}()->associate($foreign_entity);
            }
        }

        $created_model->save();
    }
}
