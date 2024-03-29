<?php

namespace Kiwilan\Steward\Livewire\Traits;

use Kiwilan\Steward\Services\Datayable\DatayableService;

/**
 * `Livewire\Component` trait to use datayable service.
 */
trait LiveDatayable
{
    use LiveAuth;

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
        $auth = $this->auth();

        foreach ($fields as $field) {
            if (isset($auth->{$field})) {
                $auth = $auth->{$field};
            }
        }

        return $auth;
    }

    public function getModel(): object
    {
        $fields = explode('.', $this->field);
        array_pop($fields);

        $model = $this->auth();

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
