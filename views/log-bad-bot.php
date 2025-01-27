<?php

declare(strict_types=1);

$handle = fopen(__DIR__ . '/../bad-bots', 'a');
fwrite($handle, $_SERVER['HTTP_USER_AGENT'] . PHP_EOL);
fclose($handle);

http_response_code(418);
