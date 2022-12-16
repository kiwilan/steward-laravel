# **laravel-steward**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kiwilan/laravel-steward.svg?style=flat-square)](https://packagist.org/packages/kiwilan/laravel-steward)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/kiwilan/laravel-steward/run-tests?label=tests&style=flat-square)](https://github.com/kiwilan/laravel-steward/actions?query=workflow%3Arun-tests+branch%3Amain)
[![codecov](https://codecov.io/gh/kiwilan/laravel-steward/branch/main/graph/badge.svg?token=CBWSPNZSRA)](https://codecov.io/gh/kiwilan/laravel-steward)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/kiwilan/laravel-steward/Fix%20PHP%20code%20style%20issues?label=code%20style&style=flat-square)](https://github.com/kiwilan/laravel-steward/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kiwilan/laravel-steward.svg?style=flat-square)](https://packagist.org/packages/kiwilan/laravel-steward)

![Run tests](https://github.com/kiwilan/laravel-steward/actions/workflows/run-tests.yml/badge.svg)
![Fix PHP code style issues](https://github.com/kiwilan/laravel-steward/actions/workflows/fix-php-code-style-issues.yml/badge.svg)

Laravel package to allow you to use some useful traits and methods in your Laravel application, works with [vite-plugin-laravel-steward](https://github.com/kiwilan/vite-plugin-laravel-steward) for front assets.

## Installation

You can install the package via composer:

```bash
composer require kiwilan/laravel-steward
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-steward-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="steward-config"
```

This is the contents of the published config file:

```php
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
    | knuckleswtf/scribe
    |--------------------------------------------------------------------------
    |
    | To custom `knuckleswtf/scribe` package.
    |
    */

    'scribe' => [
        'endpoints' => [
            // 'book' => [
            //     'class' => \App\Models\Book::class,
            //     'routes' => ['books.show', 'books.update', 'books.destroy'],
            //     'field' => 'slug',
            // ],
        ],
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
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-steward-views"
```

## Usage

```php
$steward = new Kiwilan\Steward\LaravelSteward();
echo $steward->echoPhrase('Hello, Kiwilan!');
```

## Testing

```bash
composer test
```

Coverage

```bash
composer test-coverage
```

Watch tests

```bash
composer test:watch
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ewilan Rivière](https://github.com/ewilan-riviere)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
