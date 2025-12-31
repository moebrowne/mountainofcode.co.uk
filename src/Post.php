<?php

declare(strict_types=1);

namespace MoeBrowne;

use DateTimeImmutable;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Query;
use League\CommonMark\Output\RenderedContentInterface;
use MoeBrowne\BashLanguage\BashLanguage;
use MoeBrowne\MarkdownTag\MarkdownTagExtension;
use MoeBrowne\MarkdownTag\Tag;
use MoeBrowne\OpenscadLanguage\OpenscadLanguage;
use MoeBrowne\SmartImage\SmartImageExtension;
use MoeBrowne\StlModelViewer\StlModelViewerExtension;
use Stringable;
use Tempest\Highlight\CommonMark\HighlightExtension;
use Tempest\Highlight\Highlighter;

final class Post implements Stringable
{
    private(set) RenderedContentInterface $postCache;
    private array $frameInjections = [];

    public function __construct(
        private(set) string $filePath,
    )
    {
    }

    public function parseMarkdown()
    {
        if (isset($this->postCache)) {
            return $this->postCache;
        }

        $markdownSource = file_get_contents($this->filePath);

        // Evaluate all PHP code blocks which have the magic eval comment
        $markdownSource = preg_replace_callback(
            "#```php\n//\[eval\](?<code>.+?)```#s",
            function (array $matches): string {
                ob_start();
                eval($matches['code']);

                return ob_get_clean();
            },
            $markdownSource,
        );

        // Replace magic include statements
        $markdownSource = preg_replace_callback(
            "#\+\((?<includePath>.+?)\)#s",
            function (array $matches): string {
                $path = __DIR__ . '/../' . $matches['includePath'];

                if (file_exists($path) === false) {
                    throw new \Exception('Unable to include file [' . $path . ']');
                }

                return file_get_contents($path);
            },
            $markdownSource,
        );

        // Evaluate all HTML code blocks which have the magic eval comment
        $markdownSource = preg_replace_callback(
            "#```html\n<!--\[eval(?<attrs>[^\]]+)\]-->(?<code>.+?)```#s",
            function (array $matches): string {
                $key = 'FRAME' . base64_encode(random_bytes(8));

                $this->frameInjections[$key] = '<iframe ' . $matches['attrs'] . ' srcdoc="' . htmlentities($matches['code']) . '"></iframe>';

                return $key;
            },
            $markdownSource,
        );

        $highlighter = new Highlighter()
            ->addLanguage(new OpenscadLanguage())
            ->addLanguage(new BashLanguage())
        ;

        $environment = new Environment()
            ->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new SmartImageExtension())
            ->addExtension(new MarkdownTagExtension())
            ->addExtension(new HighlightExtension($highlighter))
            ->addExtension(new TableExtension())
            ->addExtension(new StlModelViewerExtension())
            ->addExtension(new StrikethroughExtension())
            ->addExtension(new AttributesExtension())
        ;

        return $this->postCache = new MarkdownConverter($environment)
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
            ->findAll($this->parseMarkdown()->getDocument());

        $tags = array_map(
            fn(Tag $tag): string => $tag->getLiteral(),
            iterator_to_array($nodes),
        );

        asort($tags);

        return $tags;
    }

    public function getTitle(): string
    {
        return new Query()
            ->where(Query::type(Heading::class))
            ->findOne($this->parseMarkdown()->getDocument())
            ?->firstChild()
            ->getLiteral() ?? throw new \Exception('No title found');
    }

    public function getBody(): string
    {
        $content = $this->parseMarkdown()->getContent();

        foreach ($this->frameInjections as $key => $iframeHtml) {
            $content = str_replace('<p>' . $key . '</p>', $iframeHtml, $content);
        }

        return preg_replace('/<h1>[^<]+<\/h1>/', '', $content);
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