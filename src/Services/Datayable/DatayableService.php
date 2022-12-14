<?php

namespace Kiwilan\Steward\Services\Datayable;

class DatayableService
{
    /**
     * @var DatayableItem[]
     */
    public array $data;

    public string $type;

    /**
     * @param  string  $type `social` or `financial`
     */
    public static function make(string $type): self
    {
        $service = new self();
        $service->type = $type;
        switch ($type) {
            case 'social':
                $service->data = $service->socials();
                break;

            case 'financial':
                $service->data = $service->financials();
                break;

            default:
                throw new \Exception('Invalid Datayable type');
        }

        return $service;
    }

    /**
     * @return DatayableItem[]
     */
    public function get(): array
    {
        return $this->data;
    }

    public function find(string $name): ?DatayableItem
    {
        return current(array_filter($this->data, function (DatayableItem $e) use ($name) {
            if ($e->name === $name) {
                return $e;
            }

            return null;
        }));
    }

    public function merge(array $model): array
    {
        $data = [];
        /** @var DatayableItem $item */
        foreach ($this->data as $item) {
            $current = array_filter($model, fn ($social) => $social['name'] == $item->name);
            if (! empty($current)) {
                $current = array_values($current)[0];
            }
            $item->value = $current['value'] ?? null;
            $item->full_url = "{$item->url}{$item->value}";
            $item->display_url = str_replace('https://', '', $item->full_url);
            $data[$item->name] = $item;
        }

        return $data;
    }

    /**
     * @return DatayableItem[]
     */
    public function financials(): array
    {
        return $this->setDisplayUrl(FinancialData::get());
    }

    /**
     * @return DatayableItem[]
     */
    public function socials(): array
    {
        return $this->setDisplayUrl(SocialData::get());
    }

    /**
     * @param  DatayableItem[]  $data
     * @return DatayableItem[]
     */
    private function setDisplayUrl(array $data)
    {
        $list = [];
        foreach ($data as $item) {
            $item->display_url = str_replace('https://', '', $item->url);
            $list[] = $item;
        }

        return $list;
    }
}
