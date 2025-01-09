<?php

namespace MoeBrowne\StlModelViewer;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class StlModelViewerRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        return '<x-3d-model src="' . $node->getLiteral() . '"></x-3d-model><script src="/assets/stl-viewer.js"></script>';
    }

    public function getXmlTagName(Node $node): string
    {
        return 'tag';
    }

    /**
     * {@inheritDoc}
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
