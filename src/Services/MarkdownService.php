<?php

namespace Kiwilan\Steward\Services;

use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kiwilan\Steward\Services\Markdown\MarkdownFrontmatter;
use Kiwilan\Steward\Services\Markdown\MarkdownOptions;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;
use League\HTMLToMarkdown\HtmlConverter;

class MarkdownService
{
    private function __construct(
        protected string $content,
        protected string $filename,
        protected ?DateTime $date,
        protected MarkdownOptions $options,
        protected MarkdownFrontmatter $frontMatter,
        protected string $abstract = '',
        protected string $html = '',
        protected array $images = [],
        protected ?array $components = null,
        protected array $headers = [],
    ) {
    }

    public static function make(
        string $pathOrContent,
        MarkdownOptions $options = new MarkdownOptions(),
    ): self {
        $path = null;
        $filename = 'content';
        $date = null;
        $isPath = File::exists($pathOrContent);

        if (! $isPath) {
            $markdown = $pathOrContent;
        } else {
            $path = $pathOrContent;
            $markdown = File::get($path);
            $filename = File::name($path);
            $date = File::lastModified($path);
        }

        $date = Carbon::createFromTimestamp($date);

        $self = new self($markdown, $filename, $date, $options, MarkdownFrontmatter::make());
        $self->content = $self->replaceEnv();
        $self->frontMatter = $self->parseFrontMatter();
        $self->images = $self->parseImages();
        $self->html = $self->toHtml();
        $self->abstract = $self->generateAbstract();
        $self->headers = $self->parseHeaders($self->html);

        $self->slugifyHeaders();

        return $self;
    }

    private function slugifyHeaders(): void
    {
        $newHeaders = $this->headers;

        foreach ($newHeaders as $key => $header) {
            $newHeaders[$key]['id'] = Str::slug($header['id']);
            $newHeaders[$key]['original_id'] = $header['id'];
        }

        $this->headers = $newHeaders;

        $html = $this->html;

        foreach ($newHeaders as $header) {
            $html = str_replace($header['original_id'], $header['id'], $html);
        }

        $this->html = $html;
    }

    private function parseHeaders(string $html): array
    {
        $regex = '/<h([1-6]).*?>(.*?)<\/h[1-6]>/';

        preg_match_all($regex, $html, $matches);

        $headers = [];

        foreach ($matches[1] as $key => $match) {
            $level = $match;
            $text = $matches[2][$key];
            $regexId = '/<a.*?id="(.*?)".*?>.*?<\/a>/';

            preg_match_all($regexId, $text, $matchesId);

            $headers[] = [
                'level' => $level,
                'label' => strip_tags($text),
                'id' => $matchesId[1][0] ?? '',
            ];
        }

        return $headers;
    }

    private function replaceEnv(): string
    {
        $markdown = '';

        foreach ($this->options->dotenv() as $dotenv_key => $config_key) {
            $markdown = str_replace($dotenv_key, config($config_key), $this->content);
        }

        return $markdown;
    }

    private function parseFrontMatter(): MarkdownFrontmatter
    {
        $regex = '/---\n([a-zA-Z0-9_-]+:.*\n)*?---\n/';

        if (! preg_match($regex, $this->content, $matches)) {
            return MarkdownFrontmatter::make();
        }

        $front_matter = $matches[0];

        //remove first and last line
        $front_matter = preg_replace('/---\n/', '', $front_matter);
        // get first line
        $first_line = explode("\n", $matches[0])[1] ?? '';

        if (empty($first_line)) {
            return MarkdownFrontmatter::make();
        }

        $this->content = preg_replace($regex, '', $this->content);
        $this->content = preg_replace('/^\n/', '', $this->content);

        $this->extractComponents();

        $items = explode("\n", $front_matter);

        $frontMatter = [];

        foreach ($items as $item) {
            $exploded = explode(':', $item);
            $key = trim($exploded[0] ?? '');
            unset($exploded[0]);
            $value = implode(':', $exploded);
            $value = trim($value);

            // if array
            if (str_contains($value, '[') && str_contains($value, ']')) {
                $value = str_replace(['[', ']'], '', $value);
                $value = explode(',', $value);
                $value = array_map('trim', $value);

                $temp = [];

                foreach ($value as $k => $v) {
                    $temp[$k] = $this->trimString($v);
                }

                $value = $temp;
            } else {
                $value = $this->trimString($value);
            }

            if ($key) {
                $frontMatter[$key] = $value;
            }
        }

        return MarkdownFrontmatter::make($frontMatter);
    }

    private function trimString(string $value): string
    {
        // if first char is "
        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = str_replace('"', '', $value);
        }

        // if first char is '
        if (str_starts_with($value, "'") && str_ends_with($value, "'")) {
            $value = str_replace("'", '', $value);
        }

        return $value;
    }

    private function generateAbstract(): string
    {
        $abstract = str_replace("\n", ' ', $this->html);
        $abstract = strip_tags($abstract);

        return trim(mb_substr($abstract, 0, 250)).'...';
    }

    private function extractComponents(): void
    {
        $regexComponent = '/---component\n([a-zA-Z0-9_-]+:.*\n)*?\n---\n/';
        preg_match_all($regexComponent, $this->content, $matches);

        $components = [];
        $matches = array_filter($matches);

        if (empty($matches)) {
            return;
        }

        foreach ($matches as $key => $match) {
            if ($match[0] && str_contains($match[0], '---component')) {
                $match[0] = str_replace('---component', '', $match[0]);
                $match[0] = str_replace('---', '', $match[0]);
                $match[0] = trim($match[0]);

                $values = explode("\n", $match[0]);

                $item = [];

                foreach ($values as $value) {
                    $exploded = explode(':', $value, 2);
                    $k = trim($exploded[0] ?? '');
                    $v = trim($exploded[1] ?? '');

                    $item[$k] = $v;
                }

                $value = [];

                foreach ($item as $k => $v) {
                    if (array_key_exists('component_name', $item)) {
                        $value['component_name'] = $item['component_name'];
                    }

                    if (str_starts_with($v, '[') && str_ends_with($v, ']')) {
                        $v = str_replace(['[', ']'], '', $v);
                        $v = explode(',', $v);
                        $v = array_map('trim', $v);
                    }

                    if ($k !== 'component_name') {
                        $value['data'][$k] = $v;
                    }
                }

                $name = $key;

                if (array_key_exists('component_name', $item)) {
                    $name = $item['component_name'].'_'.$key;
                }
                $components[$name] = $value;
            }
        }

        $this->components = $components;
        $this->content = preg_replace($regexComponent, '', $this->content);
    }

    /**
     * @return string[]
     */
    private function parseImages(): array
    {
        $items = [];

        preg_match_all('/!\[.*?\]\((.*?)\)/', $this->content, $matches);
        $images = $matches[1];

        if (! $images && ! $this->options->imagesPath()) {
            return [];
        }

        foreach ($images as $image) {
            $item = FileUploadService::make()->upload($image, $this->options->imagesPath());

            if (! $item->isLink) {
                $this->content = str_replace($image, $item->localUrl, $this->content);
            }
        }

        return $items;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getFrontMatter(): MarkdownFrontmatter
    {
        return $this->frontMatter;
    }

    public function getAbstract(): string
    {
        return $this->abstract;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @return array{level: string, label: string, id: string}[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function revert(): string
    {
        $converter = new HtmlConverter();

        return $converter->convert($this->content);
    }

    /**
     * @return string[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function getComponents(): ?array
    {
        return $this->components;
    }

    private function toHtml(): string
    {
        $converter = new MarkdownConverter(environment: $this->getConfig());

        return $converter->convert($this->content);
    }

    private function getConfig(): Environment
    {
        $config = $this->options->config();

        return (new Environment($config))
            ->addExtension(new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension())
            ->addExtension(new \League\CommonMark\Extension\GithubFlavoredMarkdownExtension())
            ->addExtension(new \League\CommonMark\Extension\Attributes\AttributesExtension())
            ->addExtension(new \League\CommonMark\Extension\Autolink\AutolinkExtension())
            ->addExtension(new \League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension())
            ->addExtension(new \League\CommonMark\Extension\DescriptionList\DescriptionListExtension())
            ->addExtension(new \League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension())
            ->addExtension(new \League\CommonMark\Extension\ExternalLink\ExternalLinkExtension())
            ->addExtension(new \League\CommonMark\Extension\Footnote\FootnoteExtension())
            ->addExtension(new \League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension())
            // ->addExtension(new \League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension())
            ->addExtension(new \League\CommonMark\Extension\Mention\MentionExtension())
            ->addExtension(new \League\CommonMark\Extension\SmartPunct\SmartPunctExtension())
            // ->addExtension(new \League\CommonMark\Extension\Strikethrough\StrikethroughExtension())
            // ->addExtension(new \League\CommonMark\Extension\TableOfContents\TableOfContentsExtension())
            ->addExtension(new \League\CommonMark\Extension\Table\TableExtension())
            ->addExtension(new \League\CommonMark\Extension\TaskList\TaskListExtension());
    }
}
