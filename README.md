# Svg charts. Create charts as plain svg images

[![Latest Version on Packagist](https://img.shields.io/packagist/v/danielpetrica/svg-charts.svg?style=flat-square)](https://packagist.org/packages/danielpetrica/svg-charts)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/danielpetrica/svg_charts/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/danielpetrica/svg_charts/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/danielpetrica/svg_charts/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/danielpetrica/svg_charts/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/danielpetrica/svg-charts.svg?style=flat-square)](https://packagist.org/packages/danielpetrica/svg-charts)

A PHP library to create svg charts for your data. No external js, no external dependencies just plain svg images

## Installation

You can install the package via composer:

```bash
composer require danielpetrica/svg-charts
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="svg-charts-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="svg-charts-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="svg-charts-views"
```

## Usage

```php
$svgCharts = new danielpetrica\SvgCharts();
echo $svgCharts->echoPhrase('Hello, danielpetrica!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Daniel, Andrei-Daniel Petrica](https://github.com/danielpetrica)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
