<?php

declare(strict_types=1);

$width  = 700;
$height = 700;

$image = imagecreatetruecolor($width, $height);
imagesavealpha($image, true);
imagealphablending($image, false);

$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
$white = imagecolorallocate($image, 255, 255, 255);
$lightGray = imagecolorallocate($image, 250, 250, 250);
$darkGray = imagecolorallocate($image, 221, 223, 222);
$black = imagecolorallocate($image, 0, 0, 0);
$red = imagecolorallocate($image, 255, 0, 0);
$purple = imagecolorallocate($image, 216, 207, 234);

imagefill($image, 0, 0, $transparent);
imagealphablending($image, true);

imagefilledellipse(
    $image,
    $width  / 2,
    $height / 2,
    $width,
    $height,
    $purple,
);

function isOutsideCircle(int $x, int $y, int $width, int $height): bool
{
    return (
        pow($x - ($width  / 2), 2) / pow(($width  / 2), 2)
        +
        pow($y - ($height / 2), 2) / pow(($height / 2), 2)
    ) > 1;
}

function renderFrame($image): string
{
    ob_start();
    imagepng($image, null, 9);
    return ob_get_clean();
}

function placeRandomPoint($image, int $x, int $y, bool $inside): void
{
    if ($inside) {
        $colour = imagecolorallocate($image, 136, 115, 211);
    } else {
        $colour = imagecolorallocate($image, 60, 180, 80);
    }

    imagefilledrectangle($image, $x, $y, $x+3, $y+3, $colour);
}

function drawLabel($image, int $height, int $total, int $insideCount): void
{
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 68, 68, 68);

    imagefilledrectangle($image, 0, $height - 20, 350, $height, $white);

    $piApprox = number_format(4 * $insideCount / $total, 4);

    $text = 'Outside: ' . str_pad((string)($total-$insideCount), 4, ' ')
        . ' | Inside: ' . str_pad((string)$insideCount, 4, ' ')
        . ' | Pi = ' . $piApprox;

    imagestring($image, 4, 10, $height - 18, $text, $black);
}

header('Content-Type: multipart/x-mixed-replace; boundary=frameboundary');

$pointsInsideCount = 0;
$total = 800;

for ($i = 1; $i <= $total; $i++) {
    $x = mt_rand(0, $width);
    $y = mt_rand(0, $height);

    $isInsideCircle = isOutsideCircle($x, $y, $width, $height) === false;

    if ($isInsideCircle) {
        $pointsInsideCount++;
    }

    placeRandomPoint($image, $x, $y, $isInsideCircle);
    drawLabel($image, $height, $i, $pointsInsideCount);

    echo "--frameboundary" . PHP_EOL;
    echo "Content-Type: image/png" . PHP_EOL . PHP_EOL;
    echo renderFrame($image) . PHP_EOL;

    ob_flush();
    flush();

    usleep(10_000);
}
