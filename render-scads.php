<?php

declare(strict_types=1);

$scadFilePaths = glob(__DIR__ . '/public/3d/*.scad');
$modelFilter = $argv[1] ?? null;
$exportFormat = $argv[2] ?? 'update-existing';

foreach ($scadFilePaths as $scadFilePath) {
    $baseFileName = basename($scadFilePath, '.scad');
    $fileName = basename($scadFilePath);
    $exitCode = null;
    $output = null;

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

            $pngPath = dirname($scadFilePath) . '/' . $baseFileName . '.png';
            $stlPath = dirname($scadFilePath) . '/' . $baseFileName . '.stl';

            if ($exportFormat === 'update-existing') {
                if (file_exists($pngPath)) {
                    renderScadToPng($scadFilePath, $pngPath, true);
                }
                if (file_exists($stlPath)) {
                    renderScadToStl($scadFilePath, $stlPath);
                }
            } else {
                match($exportFormat) {
                    'png' => renderScadToPng($scadFilePath, $pngPath, true),
                    'stl' => renderScadToStl($scadFilePath, $stlPath),
                };
            }

            echo 'Done' . PHP_EOL;
        } else {
            foreach ($scadParts as $scadPart) {
                $scadPartFileName = str_replace(['(', ')', ';'], '', $scadPart);
                echo 'Rendering ' . $fileName . ' [' . $scadPartFileName . ']... ';

                $pngPath = dirname($scadFilePath) . '/' . $baseFileName . '_' . $scadPartFileName . '.png';
                $stlPath = dirname($scadFilePath) . '/' . $baseFileName . '_' . $scadPartFileName . '.stl';

                if ($exportFormat === 'update-existing') {
                    if (file_exists($pngPath)) {
                        renderScadPartToPng($scadFilePath, $pngPath, $scadPart, true);
                    }
                    if (file_exists($stlPath)) {
                        renderScadPartToStl($scadFilePath, $stlPath, $scadPart);
                    }
                } else {
                    match($exportFormat) {
                        'png' => renderScadPartToPng($scadFilePath, $pngPath, $scadPart, true),
                        'stl', => renderScadPartToStl($scadFilePath, $stlPath, $scadPart),
                    };
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
