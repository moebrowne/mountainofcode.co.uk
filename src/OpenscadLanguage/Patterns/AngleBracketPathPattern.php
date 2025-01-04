<?php

declare(strict_types=1);

namespace MoeBrowne\OpenscadLanguage\Patterns;

use Tempest\Highlight\IsPattern;
use Tempest\Highlight\Pattern;
use Tempest\Highlight\PatternTest;
use Tempest\Highlight\Tokens\TokenTypeEnum;

#[PatternTest(input: "import <thing.scad>", output: "<thing.scad>")]
#[PatternTest(input: "use <thing.scad>", output: "<thing.scad>")]
#[PatternTest(input: "import <dir/thing.scad>", output: "<dir/thing.scad>")]
final readonly class AngleBracketPathPattern implements Pattern
{
    use IsPattern;

    public function getPattern(): string
    {
        return "(?<match>\<[^>]+\>)";
    }

    public function getTokenType(): TokenTypeEnum
    {
        return TokenTypeEnum::VALUE;
    }
}
