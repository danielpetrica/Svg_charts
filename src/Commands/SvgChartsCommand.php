<?php

namespace danielpetrica\SvgCharts\Commands;

use Illuminate\Console\Command;

class SvgChartsCommand extends Command
{
    public $signature = 'svg-charts';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
