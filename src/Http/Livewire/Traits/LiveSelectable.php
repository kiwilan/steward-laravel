<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Illuminate\Support\Collection;

/**
 * `Livewire\Component` trait to live selectable property.
 */
trait LiveSelectable
{
    public function selected(mixed $data)
    {
        $name = $data['name'];
        $values = $data['values'];

        $this->{$name} = $values;
    }

    public function selectable(Collection|array $data, string $label = 'name', string $value = 'id'): array
    {
        $items = [];

        if ($data instanceof Collection) {
            $items = $data->mapWithKeys(fn ($item) => [
                $item->id => [
                    'label' => $item->{$label},
                    'value' => $item->{$value},
                ],
            ])->toArray();
        } else {
            foreach ($data as $key => $value) {
                $items[] = [
                    'label' => $value,
                    'value' => $key,
                ];
            }
        }

        return array_values($items);
    }
}
