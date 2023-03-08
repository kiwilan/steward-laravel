<?php

namespace Kiwilan\Steward\Enums\Api;

enum SeedsApiCategoryEnum: string
{
    case animal = 'animal';

    case building = 'building';

    case city = 'city';

    case cultural = 'cultural';

    case decoration = 'decoration';

    case food = 'food';

    case monument = 'monument';

    case nature = 'nature';

    case people = 'people';

    case relationship = 'relationship';

    case space = 'space';

    case technology = 'technology';

    case all = 'all';

    case architecture = 'architecture'; // building, city, decoration, monument

    case human = 'human'; // cultural, people, relationship

    case wildlife = 'wildlife'; // animal, nature, space
}
