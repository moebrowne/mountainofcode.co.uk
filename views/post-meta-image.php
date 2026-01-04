<?php

declare(strict_types=1);

use MoeBrowne\MetaImage;
use MoeBrowne\Post;

require __DIR__ . '/../vendor/autoload.php';

/** @var Post|null $post */
$post = pipe(
    glob(__DIR__ . '/../posts/*'),
    mapToDto: fn (string $postPath): Post => new Post($postPath),
    findPostWhichMatchesUri: fn (Post $post): bool => $post->getUrl() . '.png' === $_SERVER['REQUEST_URI'],
);

if ($post === null) {
    require __DIR__ . '/not-found.php';
    exit;
}


header('Content-Type: image/png');

new MetaImage($post->getTitle(), 'https://mountainofcode.co.uk' . $post->getUrl(), 1200, 628)
    ->output();
