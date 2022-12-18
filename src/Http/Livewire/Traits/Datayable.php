<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Kiwilan\Steward\Services\Datayable\DatayableService;

trait Datayable
{
    public string $datayable = 'social';

    public ?string $field = null;

    public bool $isAuth = false;

    private ?DatayableService $service = null;

    private ?string $relation = null;

    public function makeDatayable(): ?DatayableService
    {
        $this->service = DatayableService::make($this->datayable);
        $this->getRelation();

        return $this->service;
    }

    public function getRelation(): array
    {
        $fields = explode('.', $this->field);
        $json = $this->user;
        foreach ($fields as $field) {
            if (isset($json->{$field})) {
                $json = $json->{$field};
            }
        }

        return $json;
    }

    public function getModel(): object
    {
        $fields = explode('.', $this->field);
        array_pop($fields);

        $model = $this->user;
        foreach ($fields as $field) {
            $model = $model->{$field};
        }

        return $model;
    }

    public function getField(): string
    {
        $fields = explode('.', $this->field);

        return array_pop($fields);
    }

    public function saveDatayable(array $data)
    {
        $model = $this->getModel();
        $field = $this->getField();
        $model->{$field} = $data;
        $model->save();
    }

    public function find(mixed $name): ?object
    {
        if (empty($this->service)) {
            $this->makeDatayable();
        }

        return $this->service->find($name);
    }
}
