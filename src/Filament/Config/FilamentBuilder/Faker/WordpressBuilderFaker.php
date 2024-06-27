<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder\Faker;

use Faker\Generator;
use Illuminate\Support\Str;
use Kiwilan\Steward\Utils\Faker;

class WordpressBuilderFaker
{
    public function __construct(
        protected Generator $faker,
    ) {}

    public static function make(): array
    {
        $faker = \Faker\Factory::create();
        $instance = new WordpressBuilderFaker($faker);

        return [
            $instance->heading(),
            $instance->paragraph(),
            $instance->heading(),
            $instance->paragraph(),
            $instance->codeBlock(),
            $instance->video(),
        ];
    }

    public function heading(): array
    {
        $heading = $this->faker->numberBetween(2, 3);

        return [
            'data' => [
                'level' => "h{$heading}",
                'heading' => ucfirst($this->faker->words($this->faker->numberBetween(2, 5), true)),
            ],
            'type' => 'heading',
        ];
    }

    public function paragraph(): array
    {
        $factory = Faker::make();

        return [
            'data' => [
                'paragraph' => $factory->richText()->paragraphs(),
            ],
            'type' => 'paragraph',
        ];
    }

    public function codeBlock(): array
    {
        return [
            'data' => [
                'code-block' => "let a = 'a';",
            ],
            'type' => 'code-block',
        ];
    }

    public function video(): array
    {
        $id = Str::random(11);

        return [
            'data' => [
                'video' => "https://www.youtube.com/watch?v={$id}",
                'origin' => 'youtube',
            ],
            'type' => 'video',
        ];
    }
}
