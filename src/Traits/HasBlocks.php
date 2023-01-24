<?php

namespace Kiwilan\Steward\Traits;

trait HasBlocks
{
    public function getBlocks(string $field = 'blocks', string $field_image = 'image'): ?array
    {
        $array = $this->{$field};

        if (! $array) {
            return null;
        }

        foreach ($array as $key => $block) {
            if (array_key_exists($field_image, $block)) {
                if (method_exists($this, 'getMediable')) {
                    $array[$key][$field_image] = $this->getMediable($block[$field_image], true);
                }
            }
        }

        return $array;
    }
}
