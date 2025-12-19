<?php

declare(strict_types=1);

function createDataForFrames(string $text, array $directionMap)
{
    $gridWidth = 10;
    $gridHeight = 10;

    $frames = [];

    $position = new stdClass();
    $position->x = 0;
    $position->y = 0;

    $octal = '';

    for ($i = 0; $i < strlen($text); $i++) {
        $octal .= base_convert((string)ord($text[$i]), 10, 8);
    }

    $previousFrame = [];
    foreach (str_split($octal) as $octalBit) {
        $directionVector = $directionMap[$octalBit] ?? [0, 0];

        $position->x += $directionVector[0];
        $position->y -= $directionVector[1];

        if ($position->x > $gridWidth/2) {
            $position->x = -$gridWidth/2;
        }

        if ($position->x < -$gridWidth/2) {
            $position->x = $gridWidth/2;
        }

        if ($position->y > $gridHeight/2) {
            $position->y = -$gridHeight/2;
        }

        if ($position->y < -$gridHeight/2) {
            $position->y = $gridHeight/2;
        }

        $frame = $previousFrame;

        for ($x = -$gridWidth/2; $x <= $gridWidth/2; $x++) {
            if (isset($frame[$x]) === false) {
                $frame[$x] = [];
            }

            for ($y = -$gridHeight/2; $y <= $gridHeight/2; $y++) {
                if (isset($frame[$x][$y]) === false) {
                    $frame[$x][$y] = 0;
                }

                if ($position->x === $x && $position->y === $y) {
                    $frame[$x][$y]++;
                }
            }
        }

        $frames[] = $frame;

        $previousFrame = $frame;
    }

    return $frames;
};

function renderFrame(array $frameData) {
    $size = 400;
    $image = imagecreatetruecolor($size, $size);
    $rows = count($frameData);
    $columns = count($frameData[0]);
    $cellSize = ceil($size/ $rows);

    $backgroundColor = imagecolorallocate($image, 245, 245, 245);
    imagefill($image, 0, 0, $backgroundColor);

    $maxValue = 0;

    array_walk_recursive($frameData, function($value) use (&$maxValue) {
        $maxValue = max($maxValue, $value);
    });

    for ($x = -floor($rows/2); $x < ceil($rows/2); $x++) {
        for ($y = -floor($columns/2); $y < ceil($columns/2); $y++) {
            if ($frameData[$x][$y] === 0) {
                continue;
            }

            $purple = imagecolorallocatealpha($image, 136, 115, 211, (int)(127-(127/$maxValue)*$frameData[$x][$y]));

            imagefilledrectangle(
                $image,
                (int)floor(($x * $cellSize) + ($size / 2) - $cellSize/2),
                (int)floor(($y * $cellSize) + ($size / 2) - $cellSize/2),
                (int)floor(($x * $cellSize) + ($size / 2) + $cellSize/2),
                (int)floor(($y * $cellSize) + ($size / 2) + $cellSize/2),
                $purple,
            );
        }
    }

    ob_start();

    imagejpeg($image, null, 60);

    return ob_get_clean();
}

$directionMap = [
    $_GET['directionN'] => [0, 1],
    $_GET['directionNE'] => [1, 1],
    $_GET['directionE'] => [1, 0],
    $_GET['directionSE'] => [1, -1],
    $_GET['directionS'] => [0, -1],
    $_GET['directionSW'] => [-1, -1],
    $_GET['directionW'] => [-1, 0],
    $_GET['directionNW'] => [-1, 1],
];

// Remove empty values
unset($directionMap['']);

header('Content-Type: multipart/x-mixed-replace; boundary=frameboundary');

$text = mb_strimwidth($_GET['text'], 0, 255);

foreach (createDataForFrames($text, $directionMap) as $frameData) {
    $imageData = renderFrame($frameData);

    echo "--frameboundary" . PHP_EOL;
    echo "Content-Type: image/jpeg" . PHP_EOL;
    echo "Content-Length: " . strlen($imageData) . PHP_EOL . PHP_EOL;
    echo $imageData . PHP_EOL;

    ob_flush();
    flush();

    usleep(15_000);
}