<?php

declare(strict_types=1);

$basePath = realpath(__DIR__ . '/../public/sounds');
$audioFilePath = realpath($basePath . '/' . $_GET['fileName']);

if (
    $basePath === false ||
    $audioFilePath === false ||
    str_starts_with($audioFilePath, $basePath) === false ||
    is_file($audioFilePath) === false ||
    file_exists($audioFilePath) === false
) {
    http_response_code(400);
    require __DIR__ . '/error.php';
    exit;
}

$width = 700;
$height = 60;

$cmd = 'ffmpeg -i ' . escapeshellarg($audioFilePath)
    . ' -ac 1'                    // mono (1 channel)
    . ' -filter:a aresample=8000' // resample to 8 kHz — reduces data volume
    . ' -map 0:a'                 // select audio stream from input 0
    . ' -c:a pcm_s16le'           // encode as 16-bit signed little-endian PCM
    . ' -f data'                  // raw output format (no container)
    . ' - 2>/dev/null';

$raw = shell_exec($cmd);

if (!$raw || strlen($raw) < 2) {
    http_response_code(500);
    exit('ffmpeg failed or returned no data');
}

// Unpack as signed 16-bit little-endian integers
$samples = array_values(unpack('v*', $raw));

// Convert unsigned to signed
$samples = array_map(
    fn (int $sample): int => $sample >= 32768 ? $sample - 65536 : $sample,
    $samples,
);

$globalPeakSample = max(array_map(fn (int $sample): int => abs($sample), $samples));

$perPixelSampleGroups = array_chunk($samples, (int)ceil(count($samples) / $width));

$perPixelNormalisedPeaks = array_map(
    function (array $pixelSamples) use ($globalPeakSample, $width): float {
        $peakSample = max(array_map(fn (int $sample): int => abs($sample), $pixelSamples));

        return $peakSample / $globalPeakSample;
    },
    $perPixelSampleGroups,
);

$img = imagecreatetruecolor($width, $height);
$traceColour = imagecolorallocate($img, 29, 185, 84);
imagesavealpha($img, true);
imagealphablending($img, false);
imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
imagealphablending($img, true);

foreach ($perPixelNormalisedPeaks as $x => $peak) {
    $lineLength = (int)($peak * ($height / 2) * 0.97);

    imageline(
        $img,
        $x,
        (int)(($height / 2) - $lineLength),
        $x,
        (int)(($height / 2) + $lineLength),
        $traceColour,
    );
}

header('Content-Type: image/png');
imagepng($img);
