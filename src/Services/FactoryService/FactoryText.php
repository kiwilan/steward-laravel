<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Illuminate\Support\Carbon;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\FactoryService\Providers\ProviderSindarin;
use stdClass;

/**
 * Generate fake text.
 */
class FactoryText
{
    public function __construct(
        public FactoryService $factory,
        public bool $use_sindarin = false,
    ) {
    }

    /**
     * Generate a title.
     */
    public function title(): string
    {
        return ucfirst($this->words());
    }

    /**
     * Generate timestamps.
     *
     * @return object {`created_at`: string, `updated_at`: string}
     */
    public function timestamps(string $minimum = '-20 years'): object
    {
        $created_at = Carbon::createFromTimeString(
            $this->factory->faker->dateTimeBetween($minimum)
                ->format('Y-m-d H:i:s')
        );
        $updated_at = Carbon::createFromTimeString(
            $this->factory->faker->dateTimeBetween($created_at)
                ->format('Y-m-d H:i:s')
        );

        $timestamps = new stdClass();
        $timestamps = (new class() extends stdClass
        {
            /** @var string */
            public $created_at;

            /** @var string */
            public $updated_at;

            /** @var Carbon */
            public $created_at_carbon;

            /** @var Carbon */
            public $updated_at_carbon;
        });

        $timestamps->created_at = $created_at->format('Y-m-d H:i:s');
        $timestamps->updated_at = $updated_at->format('Y-m-d H:i:s');
        $timestamps->created_at_carbon = $created_at;
        $timestamps->updated_at_carbon = $updated_at;

        return $timestamps;
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
        for ($k = 0; $k < $this->factory->faker->numberBetween($min, $max); $k++) {
            if ($type === 'html') {
                $content .= "{$this->html()}<br><br>";
            }
            if ($type === 'markdown') {
                $content .= "{$this->markdown(image: true, link: true, code: true)}\n\n";
            }
            if (!$content) {
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

        for ($k = 0; $k < $this->factory->faker->numberBetween(2, 5); $k++) {
            $paragraph = $this->paragraph();
            if ($this->factory->faker->boolean(25)) {
                $paragraph .= " <strong>{$this->sentence()}</strong>";
            }
            if ($this->factory->faker->boolean(25)) {
                $paragraph .= " <em>{$this->sentence()}</em>";
            }
            if ($this->factory->faker->boolean(25)) {
                $paragraph .= " <code>{$this->words()}</code>";
            }
            if ($withLink && $this->factory->faker->boolean(25)) {
                $paragraph .= " <a href=\"{$this->factory->faker->url()}\">{$this->words()}</a>";
            }
            if ($withImage && $this->factory->faker->boolean(15)) {
                $paragraph = "<a href=\"{$this->factory->faker->imageUrl()}\" target=\"_blank\"><img src=\"{$this->factory->faker->imageUrl()}\" alt=\"{$this->sentence()}\" /></a>";
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
        $link_text = " [{$this->sentence()}]({$this->factory->faker->url()}) ";
        $image_text = "  ![{$this->sentence()}]({$this->factory->faker->imageUrl()})  ";

        $html = [];
        if ($this->factory->faker->boolean(25)) {
            $html[] = $bold_text;
        }
        if ($this->factory->faker->boolean(25)) {
            $html[] = $italic_text;
        }
        if ($code && $this->factory->faker->boolean(25)) {
            $html[] = $code_text;
        }
        if ($link && $this->factory->faker->boolean(25)) {
            $html[] = $link_text;
        }
        if ($image && $this->factory->faker->boolean(25)) {
            $html[] = $image_text;
        }

        for ($k = 0; $k < $this->factory->faker->numberBetween($min, $max); $k++) {
            $paragraph = $this->sentence();
            $html[] = "{$paragraph}";
        }

        for ($k = 0; $k < $this->factory->faker->numberBetween($min, $max); $k++) {
            $paragraph = $this->paragraph();
            $html[] = "{$paragraph}";
        }

        shuffle($html);
        $html = preg_replace('/\s\s+/', ' ', $html);

        return implode('', $html);
    }

    public function words(): string
    {
        return $this->use_sindarin
            ? ProviderSindarin::words(asText: true)
            : $this->factory->faker->words(asText: true);
    }

    public function sentence(): string
    {
        return $this->use_sindarin
            ? ucfirst(ProviderSindarin::words(limit: 8, asText: true) . '.')
            : $this->factory->faker->sentence();
    }

    public function paragraph(): string
    {
        $content = '';
        if ($this->use_sindarin) {
            for ($k = 0; $k < $this->factory->faker->numberBetween(2, 5); $k++) {
                $content .= $this->sentence() . ' ';
            }
        }

        return $content;
    }
}
