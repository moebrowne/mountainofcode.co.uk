<?php

namespace MoeBrowne\StlModelViewer;

use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

final class StlModelViewerParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::join(
            InlineParserMatch::string('|x|'),
            InlineParserMatch::regex('\(([^\)]+)\)'),
        );
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $inlineContext
            ->getContainer()
            ->appendChild(new StlModelViewer($inlineContext->getMatches()[3]));

        $inlineContext
            ->getCursor()
            ->advanceBy($inlineContext->getFullMatchLength());

        return true;
    }
}
