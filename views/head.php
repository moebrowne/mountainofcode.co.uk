<?php $pageTitle = isset($pageTitle) ? $pageTitle . ' - Mountain Of Code':'Mountain Of Code'; ?>
<!doctype html>
<html lang="en" itemscope itemtype="http://schema.org/Blog">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
    <link href="/assets/style.css" rel="stylesheet" type="text/css">
    <link rel="preload" href="/assets/code-style.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="/assets/code-style.css" rel="stylesheet" type="text/css"></noscript>
    <link rel="alternate" title="Blog Posts" type="application/atom+xml2" href="/feed.atom">

    <meta property="og:site_name" itemprop="name" content="Mountain Of Code">
    <meta property="og:title" itemprop="name" content="<?= $pageTitle ?>">
    <meta property="og:url" itemprop="url" content="https://www.mountainofcode.co.uk<?= e($_SERVER['REQUEST_URI']); ?>">
    <?php if ($_SERVER['REQUEST_URI'] !== '/') : ?>
        <meta property="og:image" itemprop="url" content="https://www.mountainofcode.co.uk<?= e($_SERVER['REQUEST_URI']); ?>.png">
        <meta property="og:image:width" itemprop="url" content="1200">
        <meta property="og:image:height" itemprop="url" content="628">
    <?php endif; ?>
    <meta name="description" property="og:description" itemprop="description" content="Mountain of code is a collection of web and dev ops related projects mixed with a little hacking and tinkering.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/assets/icon.svg" type="image/svg+xml">
</head>
<body>
<canvas id="hexGridEl" style="position: fixed; z-index:-1; inset: 0; "></canvas>

<h1 class="primary">
    <a href="/">M0UNTAIN 0F C0DE</a>
</h1>