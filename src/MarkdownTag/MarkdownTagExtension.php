<?php

declare(strict_types=1);

namespace MoeBrowne\MarkdownTag;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

final class MarkdownTagExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addInlineParser(new TagParser())
            ->addRenderer(Tag::class, new TagRenderer());
    }
}