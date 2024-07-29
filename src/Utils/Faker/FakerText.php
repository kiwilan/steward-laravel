<?php

namespace Kiwilan\Steward\Utils\Faker;

use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Enums\FakerTextEnum;
use Kiwilan\Steward\Utils\Api\Seeds\SeedsApi;
use Kiwilan\Steward\Utils\Faker;
use Kiwilan\Steward\Utils\Faker\Text\MeaningProvider;
use Kiwilan\Steward\Utils\Faker\Text\TextProvider;

/**
 * Generate fake text.
 */
class FakerText
{
    public function __construct(
        public Faker $faker,
        public FakerTextEnum $type = FakerTextEnum::lorem,
    ) {}

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
        $instance = new $class;

        return MeaningProvider::find();
        // $exists = $class::where($field, $category)->first();
        // $exists = DB::table($instance->getTable())->where($field, $category)->first();

        // $i = 0;

        // while ($exists) {
        //     $i++;
        //     $category = $this->category($class, $field);
        //     $exists = $class::where($field, $category)->first();

        //     if ($i > 10) {
        //         $category = $this->category($class, $field);
        //         $category = $category.' '.uniqid();

        //         $exists = $class::where($field, $category)->first();
        //     }
        // }
    }

    public function tag(?string $class = null, string $field = 'name'): string
    {
        if (! $class) {
            return MeaningProvider::find('tag');
        }

        /** @var Model */
        $model = new $class;

        return MeaningProvider::find('tag');
        // $exists = $model->where($field, $tag)->first();

        // while ($exists) {
        //     $tag = $this->category($class, $field);
        // }
    }

    public function imageUrl(): string
    {
        return SeedsApi::make()
            ->fetchPictureRandomUrl();
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

        for ($k = 0; $k < $this->faker->generator()->numberBetween(10, 20); $k++) {
            $sentence = $this->sentence();

            if (substr($sentence, -1) === '.') {
                $content .= $sentence;
            } else {
                $content .= $sentence.'.';
            }
        }

        return $content;
    }
}
