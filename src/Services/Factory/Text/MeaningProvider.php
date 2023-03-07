<?php

namespace Kiwilan\Steward\Services\Factory\Text;

class MeaningProvider
{
    public static function find(): string
    {
        $words = self::words();
        $random = $words[array_rand($words)];

        return TextProvider::capitalizeFirst($random);
    }

    /**
     * Klingon words.
     *
     * @return string[]
     */
    public static function words()
    {
        return [
            'sciences',
            'technology',
            'society',
            'pop culture',
            'vehicles',

            'discovers',
            'environment',
            'space',
            'health',

            'helmet',
            'game console',
            'open source',
            'headset',
            'information technology',
            'smart home',
            'smartphone',
            'virtual reality',
            'TV',
            'web',

            'education',
            'entertainment',
            'finance',
            'government',
            'healthcare',
            'manufacturing',

            'web culture',
            'toys',
            'sports',
            'music',
            'movies',
            'books',
            'food',
            'travel',
            'fashion',
            'art',
            'design',

            'car',
            'bike',
            'plane',
            'train',
            'boat',
            'spaceship',
        ];
    }
}
