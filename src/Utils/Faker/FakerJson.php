<?php

namespace Kiwilan\Steward\Utils\Faker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kiwilan\Steward\Services\ClassParser\ClassParserItem;
use Kiwilan\Steward\Utils\Faker;

class FakerJson
{
    public function __construct(
        public Faker $faker,
        protected ?string $class = null,
        protected ?ClassParserItem $item = null,
        protected ?Model $model = null,
        protected array $transFields = [],
    ) {}

    public function get(string $class): bool
    {
        $this->class = $class;
        $this->item = ClassParserItem::make($class);

        if (! $this->item->isModel()) {
            throw new \Exception("{$class} must be an instance of Illuminate\Database\Eloquent\Model");
        }

        $this->model = $this->item->getModel();

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
        $name = $this->item->getModel()->getTable();
        $name = Str::replace('_', '-', $name);

        $this->transFields = [];

        // Check if model has a translatable property
        if ($this->item->propertyExists('translatable')) {
            // @phpstan-ignore-next-line
            $this->transFields = $this->item->getModel()->translatable;
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
        unset($data_entity['attachments']);
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

        /** @var Model */
        $created_model = $this->class::create($data_entity);

        /** @var Model */
        $instance = new $this->class;

        if ($instance->mediable) { // @phpstan-ignore-line
            /** @var object */
            $mediable = $instance->mediable;

            foreach ($mediable as $key => $value) {
                $value = $this->setMedia($created_model);

                if ($value) {
                    $created_model->{$key} = $value;
                }
            }
            $created_model->save();
        }

        if (array_key_exists('attachments', $data)) {
            $attachments = (array) $data['attachments'];

            foreach ($attachments as $field => $path) {
                $path = database_path("{$path}");

                if (! file_exists($path)) {
                    continue;
                }

                $created_model->{$field} = $this->setMedia($created_model, $path);
                $created_model->save();
            }
        }

        if (array_key_exists('foreign', $data)) {
            $foreign = (array) $data['foreign'];

            foreach ($foreign as $relation => $value) {
                $foreign_model = "\\App\\Models\\{$value->model}";
                $foreign_field = $value->field ?? 'slug';
                $foreign_key = $value->data;

                $foreign_entity = $foreign_model::where($foreign_field, $foreign_key)->first();
                $created_model->{$relation}()->associate($foreign_entity);
            }
        }

        $created_model->save();
    }

    public function setMedia(mixed $model, ?string $path = null): ?string
    {
        if (! $model instanceof Model) {
            return null;
        }

        if (! $model->isFillable('slug') || ! $model->isFillable('picture')) {
            return null;
        }

        $table = Str::replace('_', '-', $model->getTable());
        // @phpstan-ignore-next-line
        $slug = $model->slug;

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $media_path = $path;

        if (! $path) {
            $media_path = database_path("seeders/media/{$table}/{$slug}.webp");
            $ext = 'webp';
        }

        if (File::exists($media_path)) {
            $media = File::get($media_path);

            $directory = storage_path("app/public/{$table}");

            if (! File::exists($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            $filename = uniqid().'_'."{$slug}.{$ext}";
            File::put("{$directory}/{$filename}", $media);

            return "{$table}/{$filename}";
        }

        return null;
    }
}
