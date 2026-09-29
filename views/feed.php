<?php

use MoeBrowne\Post;

require __DIR__ . '/../vendor/autoload.php';

$posts = array_map(
    fn (string $postPath): Post => new Post($postPath),
    array_reverse(glob(__DIR__ . '/../posts/*')),
);

function xe(string $string): string
{
    return htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

header('Content-Type: application/atom+xml;charset=UTF-8');
header('Access-Control-Allow-Origin: *');

?>
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title>Mountain Of Code</title>W
    <id>https://<?= $_SERVER['HTTP_HOST'] ?>/</id>
    <link rel="alternate" href="https://<?= $_SERVER['HTTP_HOST'] ?>/"/>
    <link rel="self" href="https://<?= $_SERVER['HTTP_HOST'] ?>/feed.atom"/>
    <updated><?= new DateTimeImmutable()->format(DateTimeImmutable::RFC3339) ?></updated>
    <author>
        <name>MoeBrowne</name>
    </author>

    <?php foreach ($posts as $post): ?>
        <entry>
            <title><?= xe($post->getTitle()) ?></title>
            <link rel="alternate" type="text/html" href="<?= xe($post->getUrl()) ?>"/>
            <id>https://<?= $_SERVER['HTTP_HOST'] . xe($post->getUrl()); ?></id>
            <published><?= $post->getPublishedAt()->format(DateTimeImmutable::RFC3339) ?></published>
            <updated><?= $post->getPublishedAt()->format(DateTimeImmutable::RFC3339) ?></updated>
            <content type="html"><?= xe($post->getBody()) ?></content>
            <?php foreach ($post->getTags() as $tag): ?>
                <category term="<?= xe($tag) ?>"/>
            <?php endforeach; ?>
        </entry>
    <?php endforeach; ?>
</feed>
