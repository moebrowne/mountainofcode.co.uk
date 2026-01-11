<?php

declare(strict_types=1);

$scadFilePaths = glob(__DIR__ . '/public/3d/*.scad');
$modelFilter = $argv[1] ?? null;
$exportFormat = $argv[2] ?? null;

foreach ($scadFilePaths as $scadFilePath) {
    $baseFileName = basename($scadFilePath, '.scad');
    $fileName = basename($scadFilePath);
    $exitCode = null;
    $output = null;
    $includePreview = preg_match('#^(?!.*?//).*%#m', file_get_contents($scadFilePath)) === 1;

    if ($modelFilter !== null && str_contains($fileName, $modelFilter) === false) {
        continue;
    }

    $scadParts = null;

    if (preg_match_all('@//\s*part:\s(.+?)(?=\n|$)@i', file_get_contents($scadFilePath), $scadParts) !== false) {
        $scadParts = $scadParts[1];
    }

    try {
        if ($scadParts === []) {
            echo 'Rendering ' . $fileName . '... ';

            if ($exportFormat === null || $exportFormat === 'png') {
                echo '[PNG] ';
                renderScadToPng($scadFilePath, dirname($scadFilePath) . '/' . $baseFileName . '.png');

                if ($includePreview) {
                    echo '[PNG--preview] ';
                    renderScadToPng($scadFilePath, dirname($scadFilePath) . '/' . $baseFileName . '--preview.png', true);
                }
            }

            if ($exportFormat === null || $exportFormat === 'stl') {
                echo '[STL] ';
                renderScadToStl($scadFilePath, dirname($scadFilePath) . '/' . $baseFileName . '.stl');
            }

            echo 'Done' . PHP_EOL;
        } else {
            foreach ($scadParts as $scadPart) {
                $scadPartFileName = str_replace(['(', ')', ';'], '', $scadPart);
                echo 'Rendering ' . $fileName . ' [' . $scadPartFileName . ']... ';

                if ($exportFormat === null || $exportFormat === 'png') {
                    echo '[PNG] ';
                    renderScadPartToPng($scadFilePath, dirname($scadFilePath) . '/' . $baseFileName . '_' . $scadPartFileName . '.png', $scadPart);

                    if ($includePreview) {
                        echo '[PNG--preview] ';
                        renderScadPartToPng($scadFilePath, dirname($scadFilePath) . '/' . $baseFileName . '_' . $scadPartFileName . '--preview.png', $scadPart, true);
                    }
                }

                if ($exportFormat === null || $exportFormat === 'stl') {
                    echo '[STL] ';
                    renderScadPartToStl($scadFilePath, dirname($scadFilePath) . '/' . $baseFileName . '_' . $scadPartFileName . '.stl', $scadPart);
                }

                echo 'Done' . PHP_EOL;
            }
        }
    } catch (Exception $e) {
        echo 'FAILED' . PHP_EOL . $e->getMessage() . PHP_EOL . PHP_EOL . PHP_EOL;

        continue;
    }
}

function renderScadPartToStl($scadFilePath, string $stlPath, string $part): void
{
    $tempFilePath = $scadFilePath . '.tmp';

    try {
        file_put_contents($tempFilePath, file_get_contents($scadFilePath) . "\n\n!" . $part . ";\n");

        renderScadToStl($tempFilePath, $stlPath);
    } finally {
        unlink($tempFilePath);
    }
}

function renderScadToStl($scadFilePath, string $stlPath): void
{
    exec(
        'openscad-nightly \
        --hardwarnings \
        --autocenter \
        --viewall \
        -o ' . escapeshellarg($stlPath) . ' \
        ' . escapeshellarg($scadFilePath) . ' 2>&1',
        $output,
        result_code: $exitCode,
    );

    if ($exitCode !== 0) {
        throw new Exception(implode(PHP_EOL, $output));
    }
}

function renderScadPartToPng($scadFilePath, string $pngPath, string $part, bool $preview = false): void
{
    $tempFilePath = $scadFilePath . '.tmp';

    try {
        file_put_contents($tempFilePath, file_get_contents($scadFilePath) . "\n\n!" . $part . ";\n");

        renderScadToPng($tempFilePath, $pngPath, $preview);
    } finally {
        unlink($tempFilePath);
    }
}

function renderScadToPng($scadFilePath, string $pngPath, bool $preview = false): void
{
    exec(
        'openscad-nightly \
        --hardwarnings \
        --autocenter \
        --viewall \
        --imgsize=4096,4096 \
        --colorscheme Nature \
        ' . ($preview ? '--preview ' : '') . ' \
        --render \
        -o ' . escapeshellarg($pngPath) . ' \
        ' . escapeshellarg($scadFilePath) . ' 2>&1',
        $output,
        result_code: $exitCode,
    );

    if ($exitCode !== 0) {
        throw new Exception(implode(PHP_EOL, $output));
    }

    $exitCode = null;
    $output = null;

    exec(
        'convert \
        ' . escapeshellarg($pngPath) . ' \
        -transparent "#fafafa" \
        -trim \
        -resize 650x650 \
        -bordercolor none \
        -border 25 \
        ' . escapeshellarg($pngPath) . ' 2>&1',
        $output,
        result_code: $exitCode,
    );

    if ($exitCode !== 0) {
        throw new Exception(implode(PHP_EOL, $output));
    }
}
