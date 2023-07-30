<?php

namespace Kiwilan\Steward\Services\Markdown;

class MarkdownFrontMatter
{
    /**
     * @param  array<string, mixed>  $frontMatter
     */
    protected function __construct(
        protected array $frontMatter,
    ) {
    }

    /**
     * @param  array<string, mixed> | null  $frontMatter
     */
    public static function make(array $frontMatter = null): self
    {
        if (! $frontMatter) {
            $frontMatter = [];
        }

        return new self($frontMatter);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->frontMatter;
    }

    public function get(string $key): mixed
    {
        return $this->frontMatter[$key] ?? null;
    }
}
