<?php
declare(strict_types=1);

$handle = fopen(__DIR__ . '/../bots', 'a');
fwrite($handle, $_SERVER['HTTP_USER_AGENT'] . PHP_EOL);
fclose($handle);

header('Content-Type: text/plain; charset=utf-8');

?>
User-agent: *
Disallow: /bad-bot
