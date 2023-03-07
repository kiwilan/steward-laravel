<?php

namespace Kiwilan\Steward\Services\Factory;

use Kiwilan\Steward\Enums\FactoryTextEnum;
use Kiwilan\Steward\Services\Factory\Text\TextProvider;
use Kiwilan\Steward\Services\FactoryService;

/**
 * Generate fake text.
 */
class FactoryText
{
    public function __construct(
        public FactoryService $factory,
        public FactoryTextEnum $type = FactoryTextEnum::random,
    ) {
    }

    /**
     * Generate a title.
     */
    public function title(): string
    {
        return TextProvider::capitalizeFirst($this->words());
    }

    /**
     * Generate paragraphs.
     *
     * @param  string  $type `html` | `markdown` type of paragraphs to generate.
     * @param  int  $min Minimum number of paragraphs to generate.
     * @param  int  $max Maximum number of paragraphs to generate.
     */
    public function paragraphs(string $type = 'html', int $min = 1, int $max = 5): string
    {
        $content = null;

        for ($k = 0; $k < $this->factory->faker()->numberBetween($min, $max); $k++) {
            if ($type === 'html') {
                $content .= "{$this->html()}<br><br>";
            }

            if ($type === 'markdown') {
                $content .= "{$this->markdown(image: true, link: true, code: true)}\n\n";
            }

            if (! $content) {
                $content = '`type` must be `html` or `markdown`';
            }
        }

        return $content;
    }

    /**
     * Generate a HTML paragraph.
     */
    public function html(bool $withImage = true, bool $withLink = true): string
    {
        $html = '';

        for ($k = 0; $k < $this->factory->faker()->numberBetween(2, 5); $k++) {
            $paragraph = $this->paragraph();

            if ($this->factory->faker()->boolean(25)) {
                $paragraph .= " <strong>{$this->sentence()}</strong>";
            }

            if ($this->factory->faker()->boolean(25)) {
                $paragraph .= " <em>{$this->sentence()}</em>";
            }

            if ($this->factory->faker()->boolean(25)) {
                $paragraph .= " <code>{$this->words()}</code>";
            }

            if ($withLink && $this->factory->faker()->boolean(25)) {
                $paragraph .= " <a href=\"{$this->factory->faker()->url()}\">{$this->words()}</a>";
            }

            if ($withImage && $this->factory->faker()->boolean(15)) {
                $paragraph = "<a href=\"{$this->factory->faker()->imageUrl()}\" target=\"_blank\"><img src=\"{$this->factory->faker()->imageUrl()}\" alt=\"{$this->sentence()}\" /></a>";
            }
            $html .= "<p>{$paragraph}</p>";
        }

        return $html;
    }

    /**
     * Generate a Markdown paragraph.
     */
    public function markdown(bool $image = false, bool $link = false, bool $code = false)
    {
        $min = 1;
        $max = 5;

        $bold_text = " **{$this->sentence()}** ";
        $italic_text = " *{$this->sentence()}* ";
        $code_text = " `{$this->words()}` ";
        $link_text = " [{$this->sentence()}]({$this->factory->faker()->url()}) ";
        $image_text = "  ![{$this->sentence()}]({$this->factory->faker()->imageUrl()})  ";

        $html = [];

        if ($this->factory->faker()->boolean(25)) {
            $html[] = $bold_text;
        }

        if ($this->factory->faker()->boolean(25)) {
            $html[] = $italic_text;
        }

        if ($code && $this->factory->faker()->boolean(25)) {
            $html[] = $code_text;
        }

        if ($link && $this->factory->faker()->boolean(25)) {
            $html[] = $link_text;
        }

        if ($image && $this->factory->faker()->boolean(25)) {
            $html[] = $image_text;
        }

        for ($k = 0; $k < $this->factory->faker()->numberBetween($min, $max); $k++) {
            $paragraph = $this->sentence();
            $html[] = "{$paragraph}";
        }

        for ($k = 0; $k < $this->factory->faker()->numberBetween($min, $max); $k++) {
            $paragraph = $this->paragraph();
            $html[] = "{$paragraph}";
        }

        shuffle($html);
        $html = preg_replace('/\s\s+/', ' ', $html);

        return implode('', $html);
    }

    private function text(int|false $limit = 3, bool $asText = false): string
    {
        $provider = TextProvider::make($this->type);

        return $provider->words($limit, $asText);
    }

    public function word(): string
    {
        return $this->text(1, true);
    }

    public function words(): string
    {
        return $this->text(3, true);
    }

    public function sentence(): string
    {
        return TextProvider::capitalizeFirst($this->text(8, true));
    }

    public function paragraph(): string
    {
        $content = '';

        for ($k = 0; $k < $this->factory->faker()->numberBetween(2, 5); $k++) {
            $content .= $this->sentence().' ';
        }

        return $content;
    }
}
