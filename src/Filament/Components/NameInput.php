<?php

namespace Kiwilan\Steward\Filament\Components;

use Closure;
use Filament\Forms;
use Illuminate\Support\Str;

class NameInput
{
    public static function make(
        string $field = 'name',
        string|false $metaLink = 'slug',
        string|false $metaTitle = 'meta_title',
        string $label = 'Name',
        string $helper = null,
        string $skipContext = 'edit',
        int $width = 1,
        bool $required = true,
    ): Forms\Components\TextInput {
        if (null === $helper) {
            $transGenerate = __('steward::filament.form_helper.generate');
            $fieldName = __('steward::filament.form_helper.metalink').' and '.__('steward::filament.form_helper.meta_title');

            $onlyOn = __('steward::filament.form_helper.only_on');
            $context = $skipContext === 'edit' ? 'create' : 'edit';
            $context = __("steward::filament.form_helper.{$context}");
            $helper = "{$transGenerate} {$fieldName} {$onlyOn} {$context}.";
        }

        return Forms\Components\TextInput::make($field)
            ->label($label)
            ->helperText($helper)
            ->required($required)
            ->maxLength(256)
            ->reactive()
            ->afterStateUpdated(function (string $context, Closure $set, $state) use ($metaLink, $metaTitle, $skipContext) {
                if ($skipContext === $context) {
                    return;
                }

                if ($metaLink) {
                    $set($metaLink, Str::slug($state));
                }

                if ($metaTitle) {
                    if (strlen($state) > 256) {
                        $state = substr($state, 0, 255);
                    }

                    $set($metaTitle, $state);
                }
            })
            ->columnSpan($width)
        ;
    }
}
