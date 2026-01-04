<?php

use MoeBrowne\Post;

require __DIR__ . '/../vendor/autoload.php';

/** @var Post|null $post */
$post = pipe(
    array_reverse(glob(__DIR__ . '/../posts/*')),
    mapToDto: fn (string $postPath): Post => new Post($postPath),
    findPostWhichMatchesUri: fn (Post $post): bool => $post->getUrl() === $_SERVER['REQUEST_URI']
);

if ($post === null) {
    require __DIR__ . '/not-found.php';
    exit;
}

$pageTitle = $post->getTitle();

?>
<?php require __DIR__ . "/../views/head.php"; ?>

<article itemid="<?= $post->getId(); ?>" itemprop="blogPost" itemscope="" itemtype="http://schema.org/BlogPosting">
    <meta itemprop="wordcount" content="<?= $post->getWordCount() ?>">
    <meta itemprop="datePublished" content="<?= $post->getPublishedAt()->format('Y-m-d') ?>">
    <div hidden itemprop="author" itemscope="" itemtype="http://schema.org/Person">
        <meta itemprop="name" content="MoeBrowne">
    </div>
    <meta itemprop="mainEntityOfPage" content="<?= $post->getId(); ?>">

    <h1 itemprop="headline">
        <?= $post->getTitle(); ?>
    </h1>

    <time datetime="<?= $post->getPublishedAt()->format('c'); ?>" itemprop="dateCreated">
        <?= strtoupper($post->getPublishedAt()->format('j F Y')); ?>
    </time>

    <x-tag-list>
        <?php foreach ($post->getTags() as $tag): ?>
            <x-tag><?= $tag; ?></x-tag>
        <?php endforeach; ?>
    </x-tag-list>

    <x-body>
        <?= $post->getBody(); ?>
    </x-body>
</article>

<?php require __DIR__ . "/../views/foot.php"; ?>