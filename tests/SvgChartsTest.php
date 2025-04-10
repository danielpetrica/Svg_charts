<?php

use danielpetrica\SvgCharts\SvgCharts;

test('class can be instantiated', function () {
    $chart = new SvgCharts([], 'Test Chart');
    expect($chart)->toBeInstanceOf(SvgCharts::class);
});

describe('SvgCharts functionality', function () {
    // Sample test data
    $sampleData = [
        ['subject' => 'Product A', 'timeSlice' => 'January', 'count' => 25],
        ['subject' => 'Product B', 'timeSlice' => 'January', 'count' => 15],
        ['subject' => 'Product A', 'timeSlice' => 'February', 'count' => 30],
        ['subject' => 'Product B', 'timeSlice' => 'February', 'count' => 22],
    ];

    it('initializes with correct default values', function () use ($sampleData) {
        $chart = new SvgCharts($sampleData, 'Test Chart');

        expect($chart)
            ->data->toBe($sampleData)
            ->title->toBe('Test Chart')
            ->width->toBe(400)
            ->height->toBe(300);
    });

    it('sets dimensions correctly', function () use ($sampleData) {
        $chart = new SvgCharts($sampleData, 'Test Chart');
        $chart->setDimensions(800, 600);

        expect($chart)
            ->width->toBe(800)
            ->height->toBe(600);
    });

    it('initializes default colors for subjects', function () use ($sampleData) {
        $chart = new SvgCharts($sampleData);

        $colors = $chart->getColors(); // Note: You might need to add a getter method for testing

        expect($colors)
            ->toHaveKeys(['Product A', 'Product B'])
            ->each->toBeString();
    });

    it('allows setting custom colors', function () use ($sampleData) {
        $chart = new SvgCharts($sampleData);
        $chart->setColors(['Product A' => '#FF0000']);

        $color = $chart->getColorForSubject('Product A');
        expect($color)->toBe('#FF0000');
    });

    it('generates consistent colors for unknown subjects', function () {
        $chart = new SvgCharts([]);
        $color1 = $chart->getColorForSubject('Unknown');
        $color2 = $chart->getColorForSubject('Unknown');

        expect($color1)->toBe($color2);
    });

    it('generates valid SVG output', function () use ($sampleData) {
        $chart = new SvgCharts($sampleData);
        $svg = $chart->render();

        expect($svg)
            ->toBeString()
            ->toStartWith('<?xml version="1.0" standalone="no"?>')
            ->toContain('<svg')
            ->toContain('</svg>');
    });

    it('includes chart title in SVG', function () use ($sampleData) {
        $title = 'Custom Chart Title';
        $chart = new SvgCharts($sampleData, $title);
        $svg = $chart->render();

        expect($svg)->toContain($title);
    });

    it('includes all data points in SVG', function () use ($sampleData) {
        $chart = new SvgCharts($sampleData);
        $svg = $chart->render();

        foreach ($sampleData as $point) {
            expect($svg)
                ->toContain((string) $point['count'])
                ->toContain($point['subject'])
                ->toContain($point['timeSlice']);
        }
    });

    it('saves SVG to file', function () use ($sampleData) {
        $filename = __DIR__.'/test_output.svg';
        $chart = new SvgCharts($sampleData);
        $result = $chart->renderToFile($filename);

        expect($result)->toBeTrue()
            ->and(file_exists($filename))->toBeTrue()
            ->and(file_get_contents($filename))->toBeString();

        // Clean up
        if (file_exists($filename)) {
            unlink($filename);
        }
    });

    it('handles empty data set gracefully', function () {
        $chart = new SvgCharts([], 'Empty Chart');
        $svg = $chart->render();

        expect($svg)
            ->toBeString()
            ->toStartWith('<?xml version="1.0" standalone="no"?>')
            ->toContain('No data available')
            ->toContain('</svg>');
    });

    it('handles data with missing count values', function () {
        $data = [
            ['subject' => 'Product A', 'timeSlice' => 'January'], // Missing count
            ['subject' => 'Product B', 'timeSlice' => 'January'], // Missing count
        ];

        $chart = new SvgCharts($data);
        $svg = $chart->render();

        expect($svg)
            ->toBeString()
            ->toContain('January') // Should still show the timeSlice
            ->not->toThrow(Exception::class);
    });
    it('handles data with zero counts without division errors', function () {
        $data = [
            ['subject' => 'Product A', 'timeSlice' => 'January', 'count' => 0],
            ['subject' => 'Product B', 'timeSlice' => 'January', 'count' => 0],
        ];

        $chart = new SvgCharts($data);
        $svg = $chart->render();

        expect($svg)
            ->toBeString()
            ->toContain('January')
            ->toContain('Product A')
            ->toContain('Product B')
            ->not->toThrow(DivisionByZeroError::class);
    });

    it('shows zero-height bars when all counts are zero', function () {
        $data = [
            ['subject' => 'Product A', 'timeSlice' => 'January', 'count' => 0],
            ['subject' => 'Product B', 'timeSlice' => 'February', 'count' => 0],
        ];

        $chart = new SvgCharts($data);
        $svg = $chart->render();

        // Verify that bars are rendered at the bottom (zero height)
        expect($svg)
            ->toContain('y="'.($chart->getHeight() - SvgCharts::MARGIN_BOTTOM).'"');
    });

    it('adjusts width for many data points', function () {
        $data = [];
        for ($i = 0; $i < 20; $i++) {
            $data[] = ['subject' => 'Product '.$i, 'timeSlice' => 'Month '.$i, 'count' => $i * 10];
        }

        $chart = new SvgCharts($data);
        $initialWidth = $chart->getWidth(); // Need getter
        $svg = $chart->render();
        $finalWidth = $chart->getWidth(); // Need getter

        expect($finalWidth)->toBeGreaterThan($initialWidth);
    });

});
