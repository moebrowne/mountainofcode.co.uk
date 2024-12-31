<?php

declare(strict_types=1);

namespace MoeBrowne;

use DateTimeImmutable;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Query;
use League\CommonMark\Output\RenderedContentInterface;
use MoeBrowne\MarkdownTag\MarkdownTagExtension;
use MoeBrowne\MarkdownTag\Tag;
use Stringable;
use Tempest\Highlight\CommonMark\HighlightExtension;

final class Post implements Stringable
{
    private(set) RenderedContentInterface $post;

    public function __construct(
        private(set) string $filePath,
    )
    {
        $markdownSource = file_get_contents($filePath);

        $environment = new Environment()
            ->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new MarkdownTagExtension())
            ->addExtension(new HighlightExtension())
            ->addExtension(new TableExtension())
        ;

        $this->post = new MarkdownConverter($environment)
            ->convert($markdownSource);
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        $date = explode('_', pathinfo($this->filePath, PATHINFO_FILENAME))[0];

        return new DateTimeImmutable($date);
    }

    /** @return string[] */
    public function getTags(): iterable
    {
        $nodes = new Query()
            ->where(Query::type(Tag::class))
            ->findAll($this->post->getDocument());

        $tags = array_map(
            fn(Tag $tag): string => $tag->getLiteral(),
            iterator_to_array($nodes),
        );

        asort($tags);

        return $tags;
    }

    public function getTitle(): string
    {
//        new HeadingPermalink()->
        $node = new Query()
            ->where(Query::type(Heading::class))
            ->findOne($this->post->getDocument())
            ?->firstChild()
        ;

        return $node
            ->getLiteral() ?? throw new \Exception('No title found');
    }

    public function getBody(): string
    {
        $content = $this->post->getContent();

        return str_replace('<h1>' . $this->getTitle() . '</h1>', '', $content);
    }

    public function __toString(): string
    {
        return $this->getBody();
    }

    public function getUrl(): string
    {
        return '/' . explode('_', pathinfo($this->filePath, PATHINFO_FILENAME))[1];
    }

    public function getId(): string
    {
        return pathinfo($this->filePath, PATHINFO_FILENAME);
    }

    public function getWordCount(): int
    {
        return str_word_count($this->getBody());
    }
}