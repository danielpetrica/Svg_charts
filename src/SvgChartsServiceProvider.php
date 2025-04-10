<?php

namespace danielpetrica\SvgCharts;

use danielpetrica\SvgCharts\Commands\SvgChartsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
