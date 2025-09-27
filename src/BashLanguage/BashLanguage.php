<?php

declare(strict_types=1);

namespace MoeBrowne\BashLanguage;

use MoeBrowne\BashLanguage\Patterns\SinglelineCommentPattern;
use Tempest\Highlight\Languages\Base\BaseLanguage;
use Tempest\Highlight\Languages\Php\Patterns\DoubleQuoteValuePattern;
use Tempest\Highlight\Languages\Php\Patterns\FunctionCallPattern;
use Tempest\Highlight\Languages\Php\Patterns\KeywordPattern;
use Tempest\Highlight\Languages\Php\Patterns\SingleQuoteValuePattern;
use Tempest\Highlight\Languages\Php\Patterns\VariablePattern;

class BashLanguage extends BaseLanguage
{
    public function getName(): string
    {
        return 'bash';
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

            // COMMENTS
            new SinglelineCommentPattern(),

            // PROPERTIES
            new FunctionCallPattern(),

            // VARIABLES
            new VariablePattern(),

            // KEYWORDS
            new KeywordPattern('if'),
            new KeywordPattern('fi'),
            new KeywordPattern('else'),
            new KeywordPattern('elif'),
            new KeywordPattern('false'),
            new KeywordPattern('true'),
            new KeywordPattern('function'),
            new KeywordPattern('local'),
            new KeywordPattern('export'),
            new KeywordPattern('shopt'),
            new KeywordPattern('set'),
            new KeywordPattern('alias'),
            new KeywordPattern('return'),
            new KeywordPattern('echo'),
            new KeywordPattern('while'),
            new KeywordPattern('do'),
            new KeywordPattern('done'),
            new KeywordPattern('then'),
            new KeywordPattern('for'),
            new KeywordPattern('exit'),

            // VALUES
            new SingleQuoteValuePattern(),
            new DoubleQuoteValuePattern(),
        ];
    }
}
