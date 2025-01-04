<?php

declare(strict_types=1);

namespace MoeBrowne\OpenscadLanguage;

use MoeBrowne\OpenscadLanguage\Patterns\AngleBracketPathPattern;
use MoeBrowne\OpenscadLanguage\Patterns\ListPattern;
use MoeBrowne\OpenscadLanguage\Patterns\ModuleNamePattern;
use MoeBrowne\OpenscadLanguage\Patterns\VectorPattern;
use Tempest\Highlight\Languages\Base\BaseLanguage;
use Tempest\Highlight\Languages\Php\Patterns\DoubleQuoteValuePattern;
use Tempest\Highlight\Languages\Php\Patterns\FunctionCallPattern;
use Tempest\Highlight\Languages\Php\Patterns\FunctionNamePattern;
use Tempest\Highlight\Languages\Php\Patterns\KeywordPattern;
use Tempest\Highlight\Languages\Php\Patterns\MultilineSingleDocCommentPattern;
use Tempest\Highlight\Languages\Php\Patterns\SinglelineCommentPattern;
use Tempest\Highlight\Languages\Php\Patterns\SingleQuoteValuePattern;
use Tempest\Highlight\Languages\Php\Patterns\VariablePattern;

class OpenscadLanguage extends BaseLanguage
{
    public function getName(): string
    {
        return 'openscad';
    }

    public function getInjections(): array
    {
        return [
            ...parent::getInjections(),
        ];
    }

    public function getPatterns(): array
    {
        return [
            ...parent::getPatterns(),

            new ModuleNamePattern(),
//            new FunctionNamePattern(),

            // COMMENTS
            new MultilineSingleDocCommentPattern(),
            new SinglelineCommentPattern(),

            // PROPERTIES
            new FunctionCallPattern(),

            // VARIABLES
            new VariablePattern(),

            // KEYWORDS
            new KeywordPattern('false'),
            new KeywordPattern('function'),
            new KeywordPattern('include'),
            new KeywordPattern('module'),
            new KeywordPattern('PI'),
            new KeywordPattern('true'),
            new KeywordPattern('undef'),
            new KeywordPattern('use'),

            // VALUES
            new VectorPattern(),
            new ListPattern(),
            new AngleBracketPathPattern(),
            new SingleQuoteValuePattern(),
            new DoubleQuoteValuePattern(),
        ];
    }
}
