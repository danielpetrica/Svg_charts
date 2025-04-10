<?php

namespace danielpetrica\SvgCharts\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \danielpetrica\SvgCharts\SvgCharts
 */
class SvgCharts extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \danielpetrica\SvgCharts\SvgCharts::class;
    }
}
