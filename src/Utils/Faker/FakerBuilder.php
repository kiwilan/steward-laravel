<?php

namespace Kiwilan\Steward\Utils\Faker;

use Kiwilan\Steward\Utils\Faker;

class FakerBuilder
{
    public function __construct(
        public Faker $faker,
        protected string $builder,
        protected ?string $name = null,
        protected ?string $builder_faker = null,
    ) {
    }

    public static function make(Faker $factory, string $builder): array
    {
        $factory_builder = new FakerBuilder($factory, $builder);
        $instance = new $factory_builder->builder();
        $factory_builder->name = $instance::NAME;
        $factory_builder->builder_faker = $instance::FAKER;

        $data = $factory_builder->{$factory_builder->name}();

        $faker = [];

        foreach ($data as $entry) {
            $faker[] = [
                'data' => $entry['data'],
                'type' => $entry['type'],
            ];
        }

        return $faker;
    }

    private function wordpress(): array
    {
        // [{"data": {}, "type": "heading"}, {"data": {"paragraph": "<p>paragraph</p>"}, "type": "paragraph"}, {"data": {"alt": "Sniper image", "image": "U6JwJxPG1bGbFFGu2Pc6lJ7oLDwFm7-metaZmFsbG91dF9zbmlwZXJfZmFyc2lnaHRfYnlfbWF4cGF5bnQtZDh6cngzcy5qcGc=-.jpg"}, "type": "image"}, {"data": {"video": "https://www.youtube.com/watch?v=0c9aRUTSV6U", "origin": "youtube"}, "type": "video"}, {"data": {"code-block": "let a = 'a';"}, "type": "code-block"}]

        return $this->builder_faker::make();
    }
}
