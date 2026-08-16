<?php

declare(strict_types=1);

namespace MoeBrowne;

use Composer\InstalledVersions;
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
    private const string CACHE_DIRECTORY = __DIR__ . '/../.cache';

    private(set) RenderedContentInterface $parsedMarkdownCache;
    private array $contentInjections = [];

    public function __construct(
        private(set) string $filePath,
    )
    {
    }

    public function parsedMarkdown(): RenderedContentInterface
    {
        if (isset($this->parsedMarkdownCache)) {
            return $this->parsedMarkdownCache;
        }

        $markdownSource = file_get_contents($this->filePath);

        // Evaluate all PHP code blocks which have the magic eval comment
        $markdownSource = preg_replace_callback(
            "#```php\n//\[eval\](?<code>.+?)```#s",
            function (array $matches): string {
                ob_start();
                eval($matches['code']);
                $content = ob_get_clean();

                $placeholder = "--INJECTION:" . count($this->contentInjections);
                $this->contentInjections[$placeholder] = $content;

                return $placeholder;
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

                $placeholder = "--INJECTION:" . count($this->contentInjections);
                $this->contentInjections[$placeholder] = file_get_contents($path);

                return $placeholder;
            },
            $markdownSource,
        );

        // Evaluate all HTML code blocks which have the magic eval comment
        $markdownSource = preg_replace_callback(
            "#```html\n<!--\[eval(?<attrs>[^\]]+)\]-->(?<code>.+?)```#s",
            function (array $matches): string {
                $placeholder = "--INJECTION:" . count($this->contentInjections);

                $this->contentInjections[$placeholder] = '<iframe ' . $matches['attrs'] . ' srcdoc="' . htmlentities($matches['code']) . '"></iframe>';

                return $placeholder;
            },
            $markdownSource,
        );

        $cachePath = $this->getCachePath($markdownSource);

        if (is_file($cachePath)) {
            $parsedMarkdown = unserialize(file_get_contents($cachePath), ['allowed_classes' => true]);
        }
        else {
            $parsedMarkdown = new MarkdownConverter(
                new Environment()
                    ->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new SmartImageExtension())
                    ->addExtension(new MarkdownTagExtension())
                    ->addExtension(new HighlightExtension(
                        new Highlighter()
                            ->addLanguage(new OpenscadLanguage())
                            ->addLanguage(new BashLanguage())
                    ))
                    ->addExtension(new TableExtension())
                    ->addExtension(new StlModelViewerExtension())
                    ->addExtension(new StrikethroughExtension())
                    ->addExtension(new AttributesExtension())
                )
                ->convert($markdownSource);

            if (is_dir(self::CACHE_DIRECTORY) === false) {
                mkdir(self::CACHE_DIRECTORY, recursive: true);
            }

            file_put_contents($cachePath, serialize($parsedMarkdown), LOCK_EX);
        }

        return $this->parsedMarkdownCache = $parsedMarkdown;
    }

    private function getCachePath(string $markdownSource): string
    {
        $commonMarkVersion = InstalledVersions::getVersion('league/commonmark');

        return self::CACHE_DIRECTORY . '/' . hash('sha256', $commonMarkVersion . "\0" . $markdownSource);
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
            ->findAll($this->parsedMarkdown()->getDocument());

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
            ->findOne($this->parsedMarkdown()->getDocument())
            ?->firstChild()
            ->getLiteral() ?? throw new \Exception('No title found');
    }

    public function getBody(): string
    {
        $content = $this->parsedMarkdown()->getContent();

        $this->contentInjections['CSP_NONCE'] = CSP_NONCE;

        foreach ($this->contentInjections as $key => $output) {
            $content = preg_replace('/(?:<p>)?' . preg_quote($key, '/') . '(?:<\/p>)?/', $output, $content);
        }

        if (str_contains($content, '<x-audio')) {
            $content .= '<script src="/assets/📻.js" defer></script>';
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
        return str_word_count(html_entity_decode(strip_tags($this->getBody())));
    }
}