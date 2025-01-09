<?php

namespace MoeBrowne\MarkdownTag;

use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

final class TagParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::join(
            InlineParserMatch::string('#'),
            InlineParserMatch::regex('([^\n]+)'),
        );
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $inlineContext
            ->getContainer()
            ->appendChild(new Tag($inlineContext->getMatches()[2]));

        $inlineContext
            ->getCursor()
            ->advanceBy($inlineContext->getFullMatchLength());

        return true;
    }
}
