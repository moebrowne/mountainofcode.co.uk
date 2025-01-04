<?php

declare(strict_types=1);

namespace MoeBrowne\OpenscadLanguage\Patterns;

use Tempest\Highlight\IsPattern;
use Tempest\Highlight\Pattern;
use Tempest\Highlight\PatternTest;
use Tempest\Highlight\Tokens\TokenTypeEnum;

#[PatternTest(input: "[0:1:10]", output: "[0:1:10]")]
#[PatternTest(input: "[10 : 10 : 0]", output: "[10 : 10 : 0]")]
#[PatternTest(input: "[10 : -10 : 0]", output: "[10 : -10 : 0]")]
#[PatternTest(input: "[variableName: \$specialVariable: PI]", output: "[variableName, \$specialVariable, PI]")]
final readonly class ListPattern implements Pattern
{
    use IsPattern;

    public function getPattern(): string
    {
        return "(?<match>\[([^\]]:?){1,}\])";
    }

    public function getTokenType(): TokenTypeEnum
    {
        return TokenTypeEnum::VALUE;
    }
}
