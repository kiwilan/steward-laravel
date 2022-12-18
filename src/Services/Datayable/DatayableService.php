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

    /**
     * @return DatayableItem[]
     */
    public function merge(?array $json): array
    {
        if (! $json) {
            $json = [];
        }

        $data = [];
        /** @var DatayableItem $item */
        foreach ($this->data as $key => $item) {
            $is_array = false;
            if (array_key_exists(0, $json)) {
                $is_array = true;
            }

            if ($is_array) {
                $current = array_filter($json, function ($e) use ($item) {
                    if (is_array($e) && array_key_exists('name', $e) && $e['name'] === $item->name) {
                        return $e;
                    }

                    return null;
                });
                $current = current($current);
            } else {
                $current = array_intersect_key($json, array_flip([$item->name]));
                $current = [
                    'name' => $item->name,
                    'value' => $current[$item->name] ?? null,
                ];
            }

            $item->value = $current['value'] ?? null;
            $item->full_url = "{$item->url}{$item->value}";
            $item->display_url = str_replace('https://', '', $item->full_url);
            $data[$item->name] = $item;
        }

        return $data;
    }

    public function hasData(array $json): bool
    {
        $data = $this->merge($json);

        return count(array_filter($data, function (DatayableItem $e) {
            return $e->value;
        })) > 0;
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
