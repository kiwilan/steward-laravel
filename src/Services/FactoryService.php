<?php

namespace Kiwilan\Steward\Services;

use Faker\Generator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kiwilan\Steward\Faker\FakerHtmlProvider;
use Kiwilan\Steward\Services\FactoryService\FactoryMedia;

class FactoryService
{
    public function __construct(
        public Generator $faker,
        public ?FactoryMedia $media = null,
    ) {
    }

    public static function make(): self
    {
        $faker = \Faker\Factory::create();
        $service = new FactoryService($faker);
        $service->htmlParagraphs();

        return $service;
    }

    public function setFactoryMedia()
    {
        $this->media = new FactoryMedia($this);

        return $this;
    }

    public function htmlParagraphs()
    {
        $this->faker->addProvider(new FakerHtmlProvider($this->faker));

        return $this;
    }

    /**
     * Générer un body riche.
     */
    public static function generateRichBody(Generator $faker): string
    {
        $html = '';

        $dir = 'pubStlic/uploads';

        if (! File::exists($dir)) {
            File::makeDirectory($dir);
        }

        /*
         * Generate 1 title + block
         */
        for ($i = 0; $i < $faker->numberBetween(1, 1); $i++) {
            $title = Str::title($faker->words($faker->numberBetween(5, 10), true));
            $html .= "<h2>{$title}</h2>";

            /*
             * Generate 1 subtitle + block
             */
            for ($j = 0; $j < $faker->numberBetween(1, 2); $j++) {
                $title = Str::title($faker->words($faker->numberBetween(5, 10), true));
                $html .= "<h3>{$title}</h3>";

                /*
                 *  Generate many paragraphs
                 */
                for ($k = 0; $k < $faker->numberBetween(2, 5); $k++) {
                    $paragraph = $faker->paragraph(5);
                    $html .= "<p>{$paragraph}</p>";

                    /*
                     * Add image randomly
                     */
                    // if ($faker->boolean(25)) {
                    //     $source = self::randomMediaPath($faker, 'business');
                    //     $image = basename($source);
                    //     $target = "$dir/$image";

                    //     if (! File::exists($target)) {
                    //         File::copy($source, $target);
                    //     }

                    //     $html .= "<p><img src=\"/uploads/{$image}\" alt=\"\"></p>";
                    // }
                }
            }
        }

        return $html;
    }

    public function timestamps(string $minimum = '-20 years'): array
    {
        $created_at = Carbon::createFromTimeString(
            $this->faker->dateTimeBetween($minimum)
            ->format('Y-m-d H:i:s')
        )->format('Y-m-d H:i:s');
        $updated_at = Carbon::createFromTimeString(
            $this->faker->dateTimeBetween($created_at)
            ->format('Y-m-d H:i:s')
        )->format('Y-m-d H:i:s');

        return [
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
    }
}
