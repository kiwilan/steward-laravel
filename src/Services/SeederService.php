<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SeederService
{
    public function __construct()
    {
    }

    public static function make()
    {
        return new SeederService();
    }

    public function jsonToModel(string $model): bool
    {
        /** @var Model */
        $instance = new $model();
        $name = $instance->getTable();
        $name = Str::replace('_', '-', $name);

        $is_searchable = false;
        if (method_exists($instance, 'search')) {
            $is_searchable = true;
        }

        if ($is_searchable) {
            return $model::withoutSyncingToSearch(function () use ($model) {
                return $this->parseJson($model);
            });
        }

        return $this->parseJson($model);
    }

    public function parseJson(string $model): bool
    {
        /** @var object */
        $instance = new $model();
        $name = $model;
        if (method_exists($instance, 'getTable')) {
            $name = $instance->getTable();
            $name = Str::replace('_', '-', $name);
        }

        $trans_fields = [];
        if (property_exists($instance, 'translatable')) {
            $trans_fields = $instance->translatable;
        }

        $path = database_path("seeders/data/{$name}.json");
        if (!File::exists($path)) {
            echo 'No JSON detected';

            return false;
        }
        $json = json_decode(File::get($path));
        $factory = FactoryService::make();

        foreach ($json as $entity) {
            $data = (array) $entity;
            $data_entity = $data;
            unset($data_entity['foreign']);

            $trans_values = [];
            foreach ($trans_fields as $field) {
                unset($data_entity[$field]);
                $trans_values[$field] = (array) $data[$field];
            }
            $data_entity = [
                ...$data_entity,
                ...$trans_values,
            ];

            if (class_exists($model)) {
                /** @var Model */
                $created_model = $model::create($data_entity);
                if ($created_model->isFillable('picture')) {
                    $created_model = $factory->media_local->setMedia($created_model);
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

        return true;
    }
}
