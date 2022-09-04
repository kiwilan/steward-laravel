<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Steward publishable
    |--------------------------------------------------------------------------
    |
    | For `publish:scheduled` command, set here all models with `Publishable` trait.
    |
    */

    'publishable' => [
        // \App\Models\Example::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Steward scoutable
    |--------------------------------------------------------------------------
    |
    | For `scout:fresh` command, set here all models with `Searchable` trait.
    |
    */

    'scoutable' => [
        // \App\Models\Example::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Steward scoutable
    |--------------------------------------------------------------------------
    |
    | For `scout:fresh` command, set here all models with `Searchable` trait.
    |
    */

    'mediable' => [
        // \App\Models\Example::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Steward media
    |--------------------------------------------------------------------------
    |
    | Set extensions for `Mediable` trait and `media:clean` command.
    |
    */

    'media' => [
        'default' => false,
        'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Steward filament
    |--------------------------------------------------------------------------
    |
    | To custom `filament/filament` package.
    |
    */

    'filament' => [
        'logo' => [
            'default' => env('FILAMENT_LOGO_DEFAULT', 'images/logo.svg'),
            'dark' => env('FILAMENT_LOGO_DARK', 'images/logo-dark.svg'),
        ],
        'logo-inline' => [
            'default' => env('FILAMENT_LOGO_INLINE_DEFAULT', 'images/logo-inline.svg'),
            'dark' => env('FILAMENT_LOGO_INLINE_DARK', 'images/logo-inline-dark.svg'),
        ],
        'widgets' => [
            'welcome' => [
                'url' => 'https://filamentphp.com/docs',
                'label' => 'filament::widgets/filament-info-widget.buttons.visit_documentation.label',
            ],
        ],
    ],
];
