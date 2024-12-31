<?php

declare(strict_types=1);

$path = $_SERVER['REQUEST_URI'];

match($path) {
    '/' => require __DIR__ . '/../views/post-list.php',
    default => require __DIR__ . '/../views/post.php',
};
