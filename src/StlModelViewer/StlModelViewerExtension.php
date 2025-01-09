<?php

declare(strict_types=1);

namespace MoeBrowne\StlModelViewer;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

final class StlModelViewerExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addInlineParser(new StlModelViewerParser())
            ->addRenderer(StlModelViewer::class, new StlModelViewerRenderer());
    }
}