<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Services\Datayable\DatayableService;

trait HasDatayable
{
    protected array $default_datayable = [];

    public function getDatayableFields(): array
    {
        if ($this->datayable && ! is_array($this->datayable)) {
            throw new \Exception('Invalid datayable type, must be array');
        }

        return $this->datayable ?? $this->default_datayable;
    }

    public function initializeHasDatayable()
    {
        foreach ($this->getDatayableFields() as $field) {
            $this->fillable[] = $field;
            $this->casts[$field] = 'array';
        }
    }

    protected static function bootHasDatayable()
    {
        static::saved(function (Model $model) {
            $fields = $model->datayable ?? $model->default_datayable;

            foreach ($fields as $key => $field) {
                $service = DatayableService::make($key);
                $data = $service->merge($model->{$field});

                $json = [];

                foreach ($data as $value) {
                    $json[$value->name] = $value->value;
                }

                $model->{$field} = $json;
                $model->saveQuietly();
            }
        });
    }
}
