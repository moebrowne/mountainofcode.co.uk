<?php

declare(strict_types=1);

namespace MoeBrowne\SmartImage;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\ExtensionInterface;

final class SmartImageExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addRenderer(Image::class, new SmartImageRenderer(), 10);
    }
}