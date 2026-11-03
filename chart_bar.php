<?php

    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/includes/auth_check.php';

    ini_set('display_errors', '0');

    use App\Services\ChartDataService;

    require_once __DIR__ . '/libraries/pChart/class/pData.class.php';
    require_once __DIR__ . '/libraries/pChart/class/pDraw.class.php';
    require_once __DIR__ . '/libraries/pChart/class/pImage.class.php';

    $currentYear = (int) date('Y');

    $year = isset($_GET['year']) && (int) $_GET['year'] >= 2000 && (int) $_GET['year'] <= 2100
        ? (int) $_GET['year']
        : $currentYear;

    $trend = ChartDataService::monthlyIncomeExpense($currentUserId, $year);

    $width = 900;
    $height = 560;

    $image = new pImage($width, $height, null, false);
    $image->Antialias = true;
    $image->setFontProperties([
        'FontName' => __DIR__ . '/libraries/pChart/fonts/verdana.ttf',
        'FontSize' => 13,
        'R' => 70,
        'G' => 70,
        'B' => 70,
    ]);

    $hasData = array_sum($trend['income']) + array_sum($trend['expense']) > 0;

    if (!$hasData) {
        $image->drawText($width / 2, $height / 2, 'No transactions recorded for ' . $year, [
            'FontSize' => 12,
            'Align' => TEXT_ALIGN_MIDDLEMIDDLE,
            'R' => 150,
            'G' => 150,
            'B' => 150,
        ]);
        $image->stroke(true);
        exit;
    }

    $data = new pData();
    $data->addPoints($trend['income'], 'Income');
    $data->setPalette('Income', ['R' => 39, 'G' => 174, 'B' => 96]);
    $data->setSerieDescription('Income', 'Income');

    $data->addPoints($trend['expense'], 'Expense');
    $data->setPalette('Expense', ['R' => 231, 'G' => 76, 'B' => 60]);
    $data->setSerieDescription('Expense', 'Expense');

    $data->addPoints($trend['labels'], 'Months');
    $data->setAbscissa('Months');
    $data->setAxisDisplay(0, AXIS_FORMAT_CURRENCY, APP_CURRENCY_SYMBOL);

    $image->setDataSet($data);

    $image->setGraphArea(90, 30, 860, 430);
    $image->drawFilledRectangle(90, 30, 860, 430, [
        'R' => 247,
        'G' => 248,
        'B' => 250,
        'Surrounding' => -12,
    ]);

    $image->drawScale([
        'Mode' => SCALE_MODE_START0,
        'DrawSubTicks' => false,
        'GridR' => 225,
        'GridG' => 228,
        'GridB' => 232,
        'GridAlpha' => 100,
        'LabelRotation' => 0,
        'CycleBackground' => false,
    ]);
    $image->setShadow(true, ['X' => 3, 'Y' => 3, 'R' => 0, 'G' => 0, 'B' => 0, 'Alpha' => 14]);

    $image->drawBarChart([
        'DisplayValues' => false,
        'Interleave' => 0.1,
        'Rounded' => true,
        'RoundRadius' => 6,
        'Surrounding' => -20,
    ]);

    $image->setShadow(false);

    $image->drawLegend(320, 490, [
        'Style' => LEGEND_ROUND,
        'Mode' => LEGEND_HORIZONTAL,
        'Family' => LEGEND_FAMILY_BOX,
        'BoxWidth' => 10,
        'BoxHeight' => 10,
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