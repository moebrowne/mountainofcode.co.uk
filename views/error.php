<?php
http_response_code(500);
?>
<!doctype html>
<html lang="en" itemscope itemtype="http://schema.org/Blog">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link href='/assets/style.css' rel='stylesheet' type='text/css'>

    <meta property="og:site_name" itemprop="name" content="Mountain Of Code">
    <meta property="og:url" itemprop="url" content="https://www.mountainofcode.co.uk/">
    <meta name="description" property="og:description" itemprop="description" content="Mountain of code is a collection of web and dev ops related projects mixed with a little hacking and tinkering.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background-color: #dd2c2c;">
<canvas id="hexGridEl" style="position: fixed; z-index:-1; inset: 0; "></canvas>
<script src="/assets/hexGrid.js"></script>
<script>
    hexGrid.options.corruption.percentage = 100;
    hexGrid.options.sector.colours.corrupt = [
        '#b12020',
        '#b8271e',
        '#c22620',
        '#ba1e1e',
        '#b81e1e',
        '#b51d1d',
        '#b01c1c',
    ];
    hexGrid.init(document.getElementById("hexGridEl"));
</script>

<div style="
    position: fixed;
    font-family: 'Share Tech Mono', monospace;
    font-size: 7rem;
    text-shadow: 0 0 10px 40px #000;
    color: #FFF;
    align-content: center;
    text-align: center;
    inset: 0;"
>
    ERROR
    <?php if ($_SERVER['SERVER_NAME'] === 'localhost') : ?>
    <div style="font-size: 1rem; text-align: left; background-color: rgba(0,0,0,0.6); padding: 50px; margin: 0; line-height: 1.6rem; text-wrap: nowrap; overflow-x: auto">
        <?php /** @var Throwable $exception */ ?>

        <?= str_replace(realpath(__DIR__ . '/../'), '', $exception->getFile()) ?>:<?= $exception->getLine() ?><br><br>
        <?= $exception->getMessage(); ?><br>
        <br><br>

        <?php foreach ($exception->getTrace() as $trace) : ?>
            <?php if (str_starts_with($trace['function'], '{closure:')) {
                $closure = str_replace(['{closure:', '}'], '', $trace['function']);
                [$filePath, $lineNumber] = explode(':', $closure, 2);

                $trace = [
                    'class' => 'anonymous fn',
                    'type' => '',
                    'function' => '',
                    'file' => $filePath,
                    'line' => $lineNumber,
                ];
            } ?>

            <span style="display: inline-block; width: 700px;"><?= $trace['class'] . $trace['type'] . $trace['function'] ?>()</span>
            <?= str_replace(realpath(__DIR__ . '/../'), '', $trace['file']) ?>:<?= $trace['line'] ?>
            <br>
        <?php endforeach; ?>
    </div>
    <?php endif ?>
</div>

</body>
</html>