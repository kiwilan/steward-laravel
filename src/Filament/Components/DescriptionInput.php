<?php

namespace Kiwilan\Steward\Filament\Components;

use Closure;
use Filament\Forms;

class DescriptionInput
{
    public static function make(
        string $field = 'description',
        string|false $metaField = 'meta_description',
        string $label = 'Description',
        string $helper = null,
        string $skipContext = 'edit',
        int $width = 1,
        bool $required = false,
    ): Forms\Components\Textarea {
        if (null === $helper && $metaField) {
            $transGenerate = __('steward::filament.form_helper.generate');
            $transMetaField = __('steward::filament.form_helper.meta_description');
            $onlyOn = __('steward::filament.form_helper.only_on');
            $context = $skipContext === 'edit' ? 'create' : 'edit';
            $context = __("steward::filament.form_helper.{$context}");
            $helper = "{$transGenerate} {$transMetaField} {$onlyOn} {$context}.";
        }

        return Forms\Components\Textarea::make($field)
            ->label($label)
            ->helperText($helper)
            ->required($required)
            ->reactive()
            ->afterStateUpdated(function (string $context, Closure $set, $state) use ($skipContext, $metaField) {
                if ($skipContext === $context) {
                    return;
                }

                if ($metaField) {
                    if (strlen($state) > 256) {
                        $state = substr($state, 0, 255);
                    }
                    $set($metaField, $state);
                }
            })
            ->columnSpan($width)
        ;
    }
}
