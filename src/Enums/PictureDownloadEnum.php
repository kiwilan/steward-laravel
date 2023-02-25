<?php

namespace Kiwilan\Steward\Enums;

enum PictureDownloadEnum: string
{
    case city = 'city';

    case cultural = 'cultural';

    case decoration = 'decoration';

    case food = 'food';

    case love = 'love';

    case monument = 'monument';

    case nature = 'nature';

    case people = 'people';

    case space = 'space';

    case technology = 'technology';

    // global

    case humans = 'humans'; // people, love, cultural

    case landscape = 'landscape'; // nature, city, space, monument

    case mainstream = 'mainstream'; // decoration, food, technology

    case all = 'all'; // all
}
