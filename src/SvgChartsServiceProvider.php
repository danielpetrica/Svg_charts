<?php

namespace danielpetrica\SvgCharts;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use danielpetrica\SvgCharts\Commands\SvgChartsCommand;

class SvgChartsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('svg-charts')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_svg_charts_table')
            ->hasCommand(SvgChartsCommand::class);
    }
}
