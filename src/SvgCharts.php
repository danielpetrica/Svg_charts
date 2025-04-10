<?php

namespace danielpetrica\SvgCharts;

/**
 * SvgCharts
 *
 * This class generates a bar chart in SVG format based on an array of data.
 * Each bar represents a value (count) associated with a subject, grouped by time intervals.
 * Useful for creating minimal graphical representations without external dependencies or JavaScript libraries.
 * The HTML generated by ->render() can be passed to blade for on-screen display.
 *  !For saving SVG as an image, security checks are delegated to the user!
 *  !Make sure to perform all necessary security checks!
 *
 * Data must be provided as an array of associative arrays, each containing the following keys:
 * - 'subject' => (string) the name of the subject or category
 * - 'timeSlice' => (string) time interval (e.g., "January", "2024-04", "10:00")
 * - 'count' => (int) the associated number (count, volume, etc.)
 *
 * Example of valid data:
 *
 * $data = [
 *     ['subject' => 'Product A', 'timeSlice' => 'January', 'count' => 25],
 *     ['subject' => 'Product B', 'timeSlice' => 'January', 'count' => 15],
 *     ['subject' => 'Product A', 'timeSlice' => 'February', 'count' => 30],
 *     ['subject' => 'Product B', 'timeSlice' => 'February', 'count' => 22],
 * ];
 *
 * Example usage:
 *
 * require 'SvgCharts.php'; #php way
 *
 * $chart = new SvgCharts($data, 'Monthly Sales');
 * $chart->setDimensions(800, 400);
 * # return the chart and display it on screen
 * echo $chart->render();
 *
 *
 * // or save it directly with:
 * $chart->renderToFile('chart.svg');
 */
class SvgCharts
{
    public array $data;

    public int $width;

    public int $height;

    public array $colors = [];

    public string $title;

    public array $defaultColors = [
        '#4285F4', '#EA4335', '#FBBC05', '#34A853',
        '#673AB7', '#FF5722', '#009688', '#795548',
    ];

    public const int MARGIN_LEFT = 50;

    public const int MARGIN_RIGHT = 50;

    public const int MARGIN_TOP = 50;

    public const int MARGIN_BOTTOM = 50;

    public const int BAR_WIDTH = 20;

    public const int BAR_GAP = 15;

    public const int GROUP_GAP = 40;

    /**
     * Class constructor
     *
     * @param  array  $data  Chart data (each element must contain 'subject', 'timeSlice', 'count')
     * @param  string  $title  Chart title
     */
    public function __construct(array $data, string $title = 'Barchart')
    {
        $this->data = $data;
        $this->title = $title;
        $this->width = 400;
        $this->height = 300;
        $this->initializeDefaultColors();
    }

    /**
     * Initializes default colors for subjects using the predefined list
     */
    private function initializeDefaultColors(): void
    {
        $subjects = array_unique(array_column($this->data, 'subject'));
        foreach ($subjects as $i => $subject) {
            $this->colors[$subject] = $this->defaultColors[$i % count($this->defaultColors)];
        }
    }

    /**
     * Returns the color for a subject, or generates one if missing
     */
    public function getColorForSubject(string $subject): string
    {
        $subjectKey = strtolower(trim($subject));
        foreach ($this->colors as $key => $color) {
            if (strtolower(trim($key)) === $subjectKey) {
                return $color;
            }
        }

        return $this->generateConsistentColor($subject);
    }

    /**
     * Generates a consistent color (HEX) based on the subject name
     */
    public function generateConsistentColor(string $subject): string
    {
        return '#'.substr(md5($subject), 0, 6);
    }

    /**
     * Sets chart width and height
     */
    public function setDimensions(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * Assigns custom colors to subjects
     */
    public function setColors(array $colors): self
    {
        foreach ($colors as $subject => $color) {
            $this->colors[$subject] = $color;
        }

        return $this;
    }

    /**
     * Escapes text for SVG output
     */
    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1);
    }

    /**
     * Generates the SVG chart
     *
     * @return string SVG code
     */
    public function render(): string
    {
        // Handle empty data case
        if (empty($this->data)) {
            return $this->renderEmptyChart();
        }

        $groupedData = [];
        foreach ($this->data as $item) {
            $groupedData[(string) ($item['timeSlice'] ?? 'Unknown')][] = $item;
        }

        // Get all counts, filtering out null/undefined values
        $counts = array_filter(array_column($this->data, 'count'), 'is_numeric');

        // Set max count with safe defaults (1 if empty, otherwise max value)
        $maxCount = empty($counts) ? 1 : max($counts);

        // Still ensure we never have 0 as maxCount to prevent division by zero
        $maxCount = max(1, $maxCount);

        $maxBarHeight = $this->height - self::MARGIN_TOP - self::MARGIN_BOTTOM;
        $totalGroups = count($groupedData);
        $maxItemsInGroup = max(array_map('count', $groupedData));
        $totalBarWidthPerGroup = $maxItemsInGroup * (self::BAR_WIDTH + self::BAR_GAP) - self::BAR_GAP;
        $totalChartWidth = $totalGroups * ($totalBarWidthPerGroup + self::GROUP_GAP);
        if ($totalChartWidth + self::MARGIN_LEFT + self::MARGIN_RIGHT > $this->width) {
            $this->width = $totalChartWidth + self::MARGIN_LEFT + self::MARGIN_RIGHT;
        }
        $svg = '<?xml version="1.0" standalone="no"?>';
        $svg .= '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" ';
        $svg .= '"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';
        $svg .= '<svg width="'.$this->width.'" height="'.$this->height.'" ';
        $svg .= 'viewBox="0 0 '.$this->width.' '.$this->height.'" ';
        $svg .= 'xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<title>'.$this->escape($this->title).'</title>';
        $svg .= '<rect width="100%" height="100%" fill="#f9f9f9"/>';
        $svg .= '<text x="30" y="20" font-family="Arial" font-size="20">'.$this->escape($this->title).'</text>';
        // Axes
        $svg .= '<line x1="'.self::MARGIN_LEFT.'" y1="'.($this->height - self::MARGIN_BOTTOM).'" ';
        $svg .= 'x2="'.($this->width - self::MARGIN_RIGHT).'" y2="'.($this->height - self::MARGIN_BOTTOM).'" ';
        $svg .= 'stroke="#333" stroke-width="2"/>';
        $svg .= '<line x1="'.self::MARGIN_LEFT.'" y1="'.self::MARGIN_TOP.'" ';
        $svg .= 'x2="'.self::MARGIN_LEFT.'" y2="'.($this->height - self::MARGIN_BOTTOM).'" ';
        $svg .= 'stroke="#333" stroke-width="2"/>';
        // Y-axis labels and grid
        $labelStep = max(1, (int) ceil($maxCount / 5));
        $drawnLabels = [];
        $lastY = null;
        $minSpacing = 14;
        foreach ($this->data as $item) {
            $count = (int) ($item['count'] ?? 0);
            if ($count > 0) {
                $drawnLabels[$count] = true;
            }
        }
        for ($i = 0; $i <= $maxCount; $i++) {
            if ($i % $labelStep !== 0 && ! isset($drawnLabels[$i])) {
                continue;
            }
            $y = $this->height - self::MARGIN_BOTTOM - ($i / $maxCount) * $maxBarHeight;
            if ($lastY !== null && abs($y - $lastY) < $minSpacing) {
                continue;
            }
            $lastY = $y;
            $svg .= '<line x1="'.(self::MARGIN_LEFT - 5).'" y1="'.$y.'" x2="'.self::MARGIN_LEFT.'" y2="'.$y.'" stroke="#333" stroke-width="1"/>';
            $svg .= '<line x1="'.self::MARGIN_LEFT.'" y1="'.$y.'" x2="'.($this->width - self::MARGIN_RIGHT).'" y2="'.$y.'" stroke="#ccc" stroke-dasharray="2,2"/>';
            $svg .= '<text x="'.(self::MARGIN_LEFT - 10).'" y="'.($y + 5).'" font-family="Arial" font-size="12" text-anchor="end">'.$i.'</text>';
        }
        // Bars
        $xPos = self::MARGIN_LEFT + 20;
        foreach ($groupedData as $timeSlice => $items) {
            $groupX = $xPos;
            $groupWidth = count($items) * (self::BAR_WIDTH + self::BAR_GAP) - self::BAR_GAP;
            $svg .= '<text x="'.($groupX + $groupWidth / 2).'" y="'.($this->height - 20).'" font-family="Arial" font-size="12" text-anchor="middle">'.$this->escape($timeSlice).'</text>';
            foreach ($items as $item) {
                $count = (int) ($item['count'] ?? 0);
                $barHeight = ($count / $maxCount) * $maxBarHeight;
                $yPos = $this->height - self::MARGIN_BOTTOM - $barHeight;
                $color = $this->getColorForSubject((string) $item['subject']);
                $svg .= '<rect x="'.$groupX.'" y="'.$yPos.'" width="'.self::BAR_WIDTH.'" height="'.$barHeight.'" fill="'.$color.'"/>';
                $svg .= '<text x="'.($groupX + self::BAR_WIDTH / 2).'" y="'.($yPos - 5).'" font-family="Arial" font-size="12" text-anchor="middle">'.$count.'</text>';
                $groupX += self::BAR_WIDTH + self::BAR_GAP;
            }
            $xPos += $groupWidth + self::GROUP_GAP;
        }
        // Legend
        $legendX = $this->width - 150;
        $legendY = 20;
        foreach (array_unique(array_column($this->data, 'subject')) as $subject) {
            $color = $this->colors[$subject] ?? '#999';
            $svg .= '<rect x="'.$legendX.'" y="'.$legendY.'" width="15" height="15" fill="'.$color.'"/>';
            $svg .= '<text x="'.($legendX + 20).'" y="'.($legendY + 12).'" font-family="Arial" font-size="12">'.$this->escape((string) $subject).'</text>';
            $legendY += 20;
        }
        $svg .= '</svg>';

        return $svg;
    }

    private function renderEmptyChart(): string
    {
        $svg = '<?xml version="1.0" standalone="no"?>';
        $svg .= '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" ';
        $svg .= '"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';
        $svg .= '<svg width="'.$this->width.'" height="'.$this->height.'" ';
        $svg .= 'viewBox="0 0 '.$this->width.' '.$this->height.'" ';
        $svg .= 'xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<title>'.$this->escape($this->title).'</title>';
        $svg .= '<rect width="100%" height="100%" fill="#f9f9f9"/>';
        $svg .= '<text x="50%" y="50%" font-family="Arial" font-size="16" text-anchor="middle">No data available</text>';
        $svg .= '</svg>';

        return $svg;
    }

    /**
     * Saves the SVG chart to a file
     *
     * @param  string  $filename  File path
     * @return bool True if saved successfully
     */
    public function renderToFile(string $filename): bool
    {
        return file_put_contents($filename, $this->render()) !== false;
    }

    // In SvgCharts class
    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getColors(): array
    {
        return $this->colors;
    }
}
