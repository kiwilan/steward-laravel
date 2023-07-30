<?php

namespace Kiwilan\Steward\Filament\Components;

use Closure;
use Filament\Forms;
use Kiwilan\Steward\Enums\MediaTypeEnum;

class AutoPicture
{
    public static function make(
        string $field = 'picture',
        string $label = 'Picture',
        MediaTypeEnum $type = MediaTypeEnum::media,
        array $fileTypes = [
            'image/jpeg',
            'image/webp',
            'image/png',
            'image/svg+xml',
        ],
        string $hint = 'Accepte JPG, WEBP, PNG, SVG',
        Closure $disabled = null,
    ): Forms\Components\FileUpload {
        if (! $disabled) {
            $disabled = false;
        }

        return Forms\Components\FileUpload::make($field)
            ->label($label)
            ->hint($hint)
            ->acceptedFileTypes($fileTypes)
            ->image()
            ->maxSize(1024)
            ->directory($type->name)
            ->disabled($disabled)
        ;
    }
}
