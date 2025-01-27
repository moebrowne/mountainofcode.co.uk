<?php
declare(strict_types=1);

$handle = fopen(__DIR__ . '/../bots', 'a');
fwrite($handle, $_SERVER['HTTP_USER_AGENT'] . PHP_EOL);
fclose($handle);

?>
User-agent: *
Disallow: /bad-bot
