<?php

        require_once __DIR__ . '/config/config.php';
        require_once __DIR__ . '/includes/auth_check.php';

        ini_set('display_errors', '0');

        use App\Core\Format;
        use App\Services\ChartDataService;

        require_once __DIR__ . '/libraries/pChart/class/pData.class.php';
        require_once __DIR__ . '/libraries/pChart/class/pDraw.class.php';
        require_once __DIR__ . '/libraries/pChart/class/pImage.class.php';
        require_once __DIR__ . '/libraries/pChart/class/pPie.class.php';

        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        $month = isset($_GET['month']) && (int) $_GET['month'] >= 1 && (int) $_GET['month'] <= 12
            ? (int) $_GET['month']
            : $currentMonth;

        $year = isset($_GET['year']) && (int) $_GET['year'] >= 2000 && (int) $_GET['year'] <= 2100
            ? (int) $_GET['year']
            : $currentYear;

        $breakdown = ChartDataService::expenseBreakdown($currentUserId, $month, $year);

        $width = 900;
        $height = 520;

        $image = new pImage($width, $height, null, false);
        $image->Antialias = true;
        $image->setFontProperties([
            'FontName' => __DIR__ . '/libraries/pChart/fonts/verdana.ttf',
            'FontSize' => 13,
            'R' => 70,
            'G' => 70,
            'B' => 70,
        ]);

        if (empty($breakdown)) {
            $image->drawText($width / 2, $height / 2, 'No expense data for this period', [
                'FontSize' => 12,
                'Align' => TEXT_ALIGN_MIDDLEMIDDLE,
                'R' => 150,
                'G' => 150,
                'B' => 150,
            ]);
            $image->stroke(true);
            exit;
        }

        $categoryColors = [
            'Rent' => ['R' => 231, 'G' => 76, 'B' => 60],
            'Food' => ['R' => 39, 'G' => 174, 'B' => 96],
            'Transport' => ['R' => 41, 'G' => 128, 'B' => 185],
            'Entertainment' => ['R' => 155, 'G' => 89, 'B' => 182],
        ];

        $fallbackColors = [
            ['R' => 241, 'G' => 196, 'B' => 15],
            ['R' => 26, 'G' => 188, 'B' => 156],
            ['R' => 230, 'G' => 126, 'B' => 34],
            ['R' => 149, 'G' => 165, 'B' => 166],
        ];

        $labels = [];
        $values = [];

        foreach ($breakdown as $row) {
            $amount = (float) $row['total'];
            $labels[] = $row['category'] . ' (' . Format::money($amount) . ')';
            $values[] = round($amount, 2);
        }

        $data = new pData();
        $data->addPoints($values, 'Expenses');
        $data->addPoints($labels, 'Category');
        $data->setAbscissa('Category');

        $pieChart = new pPie($image, $data);

        foreach ($breakdown as $index => $row) {
            $color = $categoryColors[$row['category']] ?? $fallbackColors[$index % count($fallbackColors)];
            $pieChart->setSliceColor($index, $color);
        }

        $image->setShadow(true, ['X' => 2, 'Y' => 2, 'R' => 0, 'G' => 0, 'B' => 0, 'Alpha' => 8]);

        $pieChart->draw2DPie(270, 260, [
            'Radius' => 160,
            'DataGapAngle' => 3,
            'DataGapRadius' => 6,
            'Border' => true,
            'BorderR' => 255,
            'BorderG' => 255,
            'BorderB' => 255,
            'WriteValues' => PIE_VALUE_PERCENTAGE,
            'ValuePosition' => PIE_VALUE_OUTSIDE,
            'ValueR' => 90,
            'ValueG' => 90,
            'ValueB' => 90,
        ]);

        $image->setShadow(false);

        $pieChart->drawPieLegend(500, 90, [
            'Style' => LEGEND_ROUND,
            'Mode' => LEGEND_VERTICAL,
            'BoxSize' => 8,
            'R' => 247,
            'G' => 248,
            'B' => 250,
            'BorderR' => 225,
            'BorderG' => 228,
            'BorderB' => 232,
            'FontR' => 60,
            'FontG' => 60,
            'FontB' => 60,
        ]);

        $image->stroke(true);