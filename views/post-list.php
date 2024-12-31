<?php

use MoeBrowne\Post;

require __DIR__ . '/../vendor/autoload.php';

$postPaths = array_reverse(glob(__DIR__ . '/../posts/*'));

$posts = array_map(fn (string $postPath): Post => new Post($postPath), $postPaths);

?>
<?php require __DIR__ . "/../views/head.php"; ?>

    <x-post-list>
        <?php $year = null; ?>
        <?php foreach ($posts as $post): ?>
            <?php if ($year === null || $year !== $post->getPublishedAt()->format('Y')) : ?>
                <?php $year = $post->getPublishedAt()->format('Y'); ?>
                <x-year-separator><?= $year; ?></x-year-separator>
            <?php endif ?>

            <article itemid="<?= $post->getId(); ?>" itemprop="blogPost" itemscope="" itemtype="http://schema.org/BlogPosting">
                <meta itemprop="wordcount" content="<?= $post->getWordCount() ?>">
                <meta itemprop="datePublished" content="<?= $post->getPublishedAt()->format('Y-m-d') ?>">
                <div hidden itemprop="author" itemscope="" itemtype="http://schema.org/Person">
                    <meta itemprop="name" content="MoeBrowne">
                </div>
                <meta itemprop="mainEntityOfPage" content="<?= $post->getId(); ?>">

                <time datetime="<?= $post->getPublishedAt()->format('c'); ?>" itemprop="dateCreated">
                    <?= strtoupper($post->getPublishedAt()->format('M jS')); ?>
                </time>

                <h1 itemprop="headline">
                    <a href="<?= $post->getUrl(); ?>" itemprop="url">
                        <?= $post->getTitle(); ?>
                    </a>
                </h1>

                <x-tag-list>
                    <?php foreach ($post->getTags() as $tag): ?>
                        <x-tag><?= $tag; ?></x-tag>
                    <?php endforeach; ?>
                </x-tag-list>
            </article>
        <?php endforeach; ?>
    </x-post-list>

<?php require __DIR__ . "/../views/foot.php"; ?>