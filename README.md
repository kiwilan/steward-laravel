# **Steward for Laravel**

![Banner with steward picture in background and Steward for Laravel title](docs/banner.jpg)

[![php][php-version-src]][php-version-href]
[![laravel][laravel-src]][laravel-href]
[![version][version-src]][version-href]
[![downloads][downloads-src]][downloads-href]
[![license][license-src]][license-href]
[![tests][tests-src]][tests-href]
[![codecov][codecov-src]][codecov-href]
[![netlify][netlify-src]][netlify-href]

PHP package for Laravel to allow you to use some useful traits and methods in your Laravel application.

<!-- PHP package for Laravel to allow you to use some useful traits and methods in your Laravel application, works with [vite-plugin-steward-laravel](https://www.npmjs.com/package/@kiwilan/vite-plugin-steward-laravel) for front assets. -->

## EXPERIMENTAL

This package is still in development and is not ready for production.

## Documentation

See [steward-for-laravel.netlify.app](https://steward-for-laravel.netlify.app) for documentation.

## Installation

You can install the package via composer:

```bash
composer require kiwilan/steward-laravel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="steward-config"
```

<!-- ### Vite plugin

```bash
npm install --save-dev @kiwilan/vite-plugin-steward-laravel
```

```bash
pnpm add @kiwilan/vite-plugin-steward-laravel -D
```

Check [@kiwilan/vite-plugin-steward-laravel](https://github.com/kiwilan/steward-laravel/tree/main/lib) for usage. -->

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

[<img src="https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg" height="120rem" width="100%" />](https://github.com/kiwilan)

[version-src]: https://img.shields.io/packagist/v/kiwilan/steward-laravel.svg?style=flat-square&colorA=18181B&colorB=777BB4
[version-href]: https://packagist.org/packages/kiwilan/steward-laravel
[php-version-src]: https://img.shields.io/static/v1?style=flat-square&label=PHP&message=≥v8.1&color=777BB4&logo=php&logoColor=ffffff&labelColor=18181b
[php-version-href]: https://www.php.net/
[downloads-src]: https://img.shields.io/packagist/dt/kiwilan/steward-laravel.svg?style=flat-square&colorA=18181B&colorB=777BB4
[downloads-href]: https://packagist.org/packages/kiwilan/steward-laravel
[license-src]: https://img.shields.io/github/license/kiwilan/steward-laravel.svg?style=flat-square&colorA=18181B&colorB=777BB4
[license-href]: https://github.com/kiwilan/steward-laravel/blob/main/README.md
[tests-src]: https://img.shields.io/github/actions/workflow/status/kiwilan/steward-laravel/run-tests.yml?branch=main&label=tests&style=flat-square&colorA=18181B
[tests-href]: https://github.com/kiwilan/steward-laravel/actions/workflows/run-tests.yml
[codecov-src]: https://codecov.io/gh/kiwilan/steward-laravel/branch/main/graph/badge.svg?token=P9XIK2KV9G
[codecov-href]: https://codecov.io/gh/kiwilan/steward-laravel
[laravel-src]: https://img.shields.io/static/v1?label=Laravel&message=≥v9&style=flat-square&colorA=18181B&colorB=FF2D20
[laravel-href]: https://laravel.com
[netlify-src]: https://api.netlify.com/api/v1/badges/849d4a45-1236-4f9e-992c-4a242588aeac/deploy-status
[netlify-href]: https://app.netlify.com/sites/steward-for-laravel/deploys
