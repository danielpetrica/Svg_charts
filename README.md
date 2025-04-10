
# Svg Charts - Create charts as plain SVG images

[![Latest Version on Packagist](https://img.shields.io/packagist/v/danielpetrica/svg-charts.svg?style=flat-square)](https://packagist.org/packages/danielpetrica/svg-charts)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/danielpetrica/svg_charts/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/danielpetrica/svg_charts/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/danielpetrica/svg_charts/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/danielpetrica/svg_charts/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/danielpetrica/svg-charts.svg?style=flat-square)](https://packagist.org/packages/danielpetrica/svg-charts)

A PHP library to create SVG charts for your data. No external JavaScript, no external dependencies - just plain SVG images.

## Features

- Generates bar charts in SVG format based on array data
- Each bar represents a value (count) associated with a subject, grouped by time intervals
- Minimal graphical representations without external dependencies or JavaScript libraries
- Output can be passed to Blade for on-screen display
- Built-in color scheme with automatic color generation

> **Security Note**: For saving SVG as an image, security checks are delegated to the user. Make sure to perform all necessary security checks!

## Installation

Install the package via composer:

```bash
composer require danielpetrica/svg-charts
```

Publish and run migrations:

```bash
php artisan vendor:publish --tag="svg-charts-migrations"
php artisan migrate
```

Publish the config file:

```bash
php artisan vendor:publish --tag="svg-charts-config"
```

Config file contents (`config/svg-charts.php`):

```php
return [
    // Configuration options will go here
];
```

Optionally publish views:

```bash
php artisan vendor:publish --tag="svg-charts-views"
```

## Usage

### Data Format Requirements

Data must be provided as an array of associative arrays with these keys:

```php
[
    'subject'   => (string) Subject/category name,
    'timeSlice' => (string) Time interval (e.g., "January", "2024-04"),
    'count'     => (int) The numeric value to display
]
```

Example dataset:

```php
$data = [
    ['subject' => 'Product A', 'timeSlice' => 'January', 'count' => 25],
    ['subject' => 'Product B', 'timeSlice' => 'January', 'count' => 15],
    ['subject' => 'Product A', 'timeSlice' => 'February', 'count' => 30],
    ['subject' => 'Product B', 'timeSlice' => 'February', 'count' => 22],
];
```

### Basic Usage

```php
use DanielPetrica\SvgCharts\SvgCharts;

// Create chart instance
$chart = new SvgCharts($data, 'Monthly Sales');

// Set dimensions (width, height in pixels)
$chart->setDimensions(800, 400);

// Output to screen
echo $chart->render();

// Save to file
$chart->renderToFile('chart.svg');
```

### Laravel Blade Integration

```html
<div class="chart-container">
    {!! $chart->render() !!}
</div>
```

### Customizing Colors

```php
// Set custom colors for subjects
$chart->setColors([
    'Product A' => '#FF5733',
    'Product B' => '#33FF57'
]);
```

## Testing

Run the test suite:

```bash
composer test
```

## Changelog

See [CHANGELOG](CHANGELOG.md) for version history.

## Contributing

Contributions welcome! See [CONTRIBUTING](CONTRIBUTING.md) for guidelines.

## Security

Please report vulnerabilities via [our security policy](../../security/policy).

## Credits

- [Daniel, Andrei-Daniel Petrica](https://github.com/danielpetrica)

## License

MIT License. See [LICENSE](LICENSE.md) for details.
```

This markdown file includes:

1. Header with badges
2. Clear feature list
3. Security notice
4. Installation instructions
5. Usage examples with code blocks
6. Data format requirements
7. Testing information
8. Standard open-source sections (Changelog, Contributing, Security, Credits, License)

The formatting uses proper markdown syntax for:
- Headers (`#`, `##`, `###`)
- Code blocks (```)
- Lists (`-`)
- Emphasis (`**`)
- Links (`[text](url)`)
- Escape characters where needed

The content is organized logically from high-level overview to specific implementation details.
