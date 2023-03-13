<?php

namespace Kiwilan\Steward\Enums\Api;

enum SeedsApiCategoryEnum: string
{
    case animal = 'animal';

    case artist = 'artist';

    case building = 'building';

    case city = 'city';

    case corporate = 'corporate';

    case cultural = 'cultural';

    case decoration = 'decoration';

    case flower = 'flower';

    case food = 'food';

    case house = 'house';

    case monument = 'monument';

    case nature = 'nature';

    case people = 'people';

    case relationship = 'relationship';

    case space = 'space';

    case sport = 'sport';

    case technology = 'technology';

    case tvshow = 'tvshow';

    case all = 'all';

    case architecture = 'architecture'; // `building`, `city`, `decoration`, `house`, `monument`

    case human = 'human'; // `artist`, `corporate`, `cultural`, `people`, `relationship`, `sport`

    case wildlife = 'wildlife'; // `animal`, `flower`, `nature`, `space`

    case entertainment = 'entertainment'; // `tvshow`

    case mainstream = 'mainstream'; // `building`, `city`, `corporate`, `decoration`, `food`, `house`, `monument`, `nature`, `people`, `technology`
}
