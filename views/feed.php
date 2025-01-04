<?php

use MoeBrowne\Post;

require __DIR__ . '/../vendor/autoload.php';

$posts = array_map(
    fn (string $postPath): Post => new Post($postPath),
    array_reverse(glob(__DIR__ . '/../posts/*')),
);

header('Content-Type: application/atom+xml;charset=UTF-8');

?>
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title>Mountain Of Code</title>
    <id>https://<?= $_SERVER['HTTP_HOST'] ?>/</id>
    <link rel="alternate" href="https://<?= $_SERVER['HTTP_HOST'] ?>/"/>
    <link rel="self" href="https://<?= $_SERVER['HTTP_HOST'] ?>/feed.atom"/>
    <updated><?= new DateTimeImmutable()->format(DateTimeImmutable::RFC3339) ?></updated>
    <author>
        <name>MoeBrowne</name>
    </author>

    <?php foreach ($posts as $post): ?>
        <entry>
            <title><![CDATA[<?= $post->getTitle() ?>]]></title>
            <link rel="alternate" type="text/html" href="<?= $post->getUrl(); ?>"/>
            <id>https://<?= $_SERVER['HTTP_HOST'] . $post->getUrl(); ?></id>
            <published><?= $post->getPublishedAt()->format(DateTimeImmutable::RFC3339) ?></published>
            <updated><?= $post->getPublishedAt()->format(DateTimeImmutable::RFC3339) ?></updated>
            <content type="html"><![CDATA[<?= $post->getBody() ?>]]></content>
            <?php foreach ($post->getTags() as $tag): ?>
                <category term="<?= $tag ?>"/>
            <?php endforeach; ?>
        </entry>
    <?php endforeach; ?>
</feed>
