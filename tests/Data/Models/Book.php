<?php

namespace Kiwilan\Steward\Tests\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Traits\HasSeo;
use Kiwilan\Steward\Traits\HasSlug;
use Kiwilan\Steward\Traits\HasTimeToRead;

class Book extends Model
{
    use HasSeo;
    use HasSlug;
    use HasTimeToRead;

    protected $slugColumn = 'slug_custom';

    protected $slugWith = 'title';

    protected $metaTitleFrom = 'title';

    protected $metaDescriptionFrom = 'description_custom';

    protected $timeToReadWith = 'body_custom';

    protected $timeToReadColumn = 'time_to_read_custom';

    protected $fillable = [
        'id',
        'title',
        'author',
        'description_custom',
        'year',
        'language_slug',
        'body_custom',
        'time_to_read_custom',
    ];

    protected $casts = [
        'id' => 'integer',
        'year' => 'integer',
    ];
}
