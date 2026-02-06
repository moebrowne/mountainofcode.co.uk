<?php

use MoeBrowne\Post;

require __DIR__ . '/../../vendor/autoload.php';

$postModelPaths = pipe(
    glob(__DIR__ . '/../../posts/*'),
    mapToDto: fn (string $postPath): Post => new Post($postPath),
    sortByDateThenTitle: fn (Post $a, Post $b): int => $b->getPublishedAt() <=> $a->getPublishedAt() ?: $a->getTitle() <=> $b->getTitle(),
    keyByPostUrl: fn (Post $post): string => $post->getUrl(),
    mapToStlPaths: function (Post $post): array {
        $matches = [];
        preg_match_all('#\|x\|\(([^)]+)\)#', file_get_contents($post->filePath), $matches);

        return $matches[1] ?? [];
    },
    mapToImagePaths: function (array $modelsPaths): array {
        return pipe(
            $modelsPaths,
            mapToImagePath: fn (string $modelPath): string => str_replace('.stl', '.png', $modelPath),
            filterOutMissingImages: fn (string $modelImagePath): string => file_exists(__DIR__ . '/../../public/' . $modelImagePath),
        );
    },
    filterOutPostsWithoutModels: fn (array $modelsPaths): bool => $modelsPaths !== [],
);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>3D Models</title>
    <link href="/assets/style.css" rel="stylesheet" type="text/css">
    <link rel="preload" href="/assets/code-style.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="/assets/code-style.css" rel="stylesheet" type="text/css"></noscript>
    <style>
        body {
            padding: 40px 20px;
            background-color: whitesmoke;
            background-image: repeating-linear-gradient(
                    -35deg,
                    transparent,
                    transparent 10px,
                    white 10px,
                    white 20px
            );
            background-attachment: fixed;
        }

        x-model-list {
            position: relative;
            max-width: 1200px;
            min-height: calc(100vh - 80px);
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        x-model-list img {
            max-height: 250px;
        }

        @media (max-width: 768px) {
            x-model-list{
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            x-model-list {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <meta property="og:site_name" itemprop="name" content="Mountain Of Code">
    <meta property="og:title" itemprop="name" content="3D Models">
    <meta name="description" property="og:description" itemprop="description" content="Mountain of code is a collection of web and dev ops related projects mixed with a little hacking and tinkering.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/assets/icon.svg" type="image/svg+xml">
</head>
<body>

<h1 class="primary">
    <a href="/">3D M0DELS</a>
</h1>

<x-model-list>
    <?php foreach ($postModelPaths as $postUrl => $modelPaths) : ?>
        <?php foreach ($modelPaths as $modelPath) : ?>
            <div>
                <a href="<?= e($postUrl) ?>">
                    <img src="<?= e(str_replace('.stl', '.png', $modelPath)) ?>">
                </a>
            </div>
        <?php endforeach ?>
    <?php endforeach ?>
</x-model-list>

</body>
</html>