<?php

declare(strict_types=1);

namespace MoeBrowne\OpenscadLanguage\Patterns;

use Tempest\Highlight\IsPattern;
use Tempest\Highlight\Pattern;
use Tempest\Highlight\PatternTest;
use Tempest\Highlight\Tokens\TokenTypeEnum;

#[PatternTest(input: 'module Foo()', output: 'Foo')]
#[PatternTest(input: 'module Foo(param1)', output: 'Foo')]
final readonly class ModuleNamePattern implements Pattern
{
    use IsPattern;

    public function getPattern(): string
    {
        return 'module (?<match>[\w]+)';
    }

    public function getTokenType(): TokenTypeEnum
    {
        return TokenTypeEnum::TYPE;
    }
}
