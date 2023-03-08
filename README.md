# **steward-laravel**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kiwilan/steward-laravel.svg?style=flat-square)](https://packagist.org/packages/kiwilan/steward-laravel)
[![npm](https://img.shields.io/npm/v/@kiwilan/vite-plugin-steward-laravel.svg?style=flat-square&color=CB3837&logo=npm&logoColor=ffffff&label=npm)](https://www.npmjs.com/package/@kiwilan/vite-plugin-steward-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kiwilan/steward-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kiwilan/steward-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kiwilan/steward-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kiwilan/steward-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kiwilan/steward-laravel.svg?style=flat-square)](https://packagist.org/packages/kiwilan/steward-laravel)

[![codecov](https://codecov.io/gh/kiwilan/steward-laravel/branch/main/graph/badge.svg?token=CBWSPNZSRA)](https://codecov.io/gh/kiwilan/steward-laravel)
[![Netlify Status](https://api.netlify.com/api/v1/badges/849d4a45-1236-4f9e-992c-4a242588aeac/deploy-status)](https://app.netlify.com/sites/steward-laravel/deploys)

PHP package for Laravel to allow you to use some useful traits and methods in your Laravel application, works with [vite-plugin-steward-laravel](https://www.npmjs.com/package/@kiwilan/vite-plugin-steward-laravel) for front assets.

## Documentation

See [steward-laravel.netlify.app](https://steward-laravel.netlify.app/) for documentation.

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
