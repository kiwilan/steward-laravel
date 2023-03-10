<?php

namespace Kiwilan\Steward\Services\Factory;

use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Enums\FactoryTextEnum;
use Kiwilan\Steward\Services\Api\Seeds\SeedsApi;
use Kiwilan\Steward\Services\Factory\Text\MeaningProvider;
use Kiwilan\Steward\Services\Factory\Text\TextProvider;
use Kiwilan\Steward\Services\FactoryService;

/**
 * Generate fake text.
 */
class FactoryText
{
    public function __construct(
        public FactoryService $factory,
        public FactoryTextEnum $type = FactoryTextEnum::lorem,
    ) {
    }

    /**
     * Generate a title.
     */
    public function title(): string
    {
        return TextProvider::capitalizeFirst($this->words());
    }

    public function category(?string $class = null, string $field = 'name'): string
    {
        if (! $class) {
            return MeaningProvider::find();
        }

        /** @var Model */
        $model = new $class();
        $category = MeaningProvider::find();
        $exists = $model::where($field, $category)->first();

        while ($exists) {
            $category = $this->category($class, $field);
        }

        return $category;
    }

    public function tag(?string $class = null, string $field = 'name'): string
    {
        if (! $class) {
            return MeaningProvider::find('tag');
        }

        /** @var Model */
        $model = new $class();
        $tag = MeaningProvider::find('tag');
        $exists = $model::where($field, $tag)->first();

        while ($exists) {
            $tag = $this->category($class, $field);
        }

        return $tag;
    }

    public function imageUrl(): string
    {
        return SeedsApi::make()
            ->fetchPictureRandomUrl()
        ;
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
