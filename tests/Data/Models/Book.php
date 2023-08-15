<?php

namespace Kiwilan\Steward\Tests\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kiwilan\Steward\Tests\Data\Resources\BookResource;
use Kiwilan\Steward\Traits\HasSeo;
use Kiwilan\Steward\Traits\HasSlug;
use Kiwilan\Steward\Traits\HasTimeToRead;
use Kiwilan\Steward\Traits\Publishable;
use Kiwilan\Steward\Traits\Queryable;

class Book extends Model
{
    use HasSeo;
    use HasSlug;
    use HasTimeToRead;
    use Queryable;
    use Publishable;

    protected $slugColumn = 'slug_custom';

    protected $slugWith = 'title';

    protected $metaTitleFrom = 'title';

    protected $metaDescriptionFrom = 'description_custom';

    protected $timeToReadWith = 'body_custom';

    protected $timeToReadColumn = 'time_to_read_custom';

    protected $queryWith = ['author'];

    protected $queryWithCount = ['author'];

    protected $queryDefaultSort = 'slug_sort';

    protected $queryAllowedFilters = ['title'];

    protected $queryAllowedSorts = ['id', 'title', 'slug_sort'];

    protected $queryNoPaginate = false;

    protected $queryPagination = 32;

    protected $queryExport = null;

    protected $queryResource = BookResource::class;

    protected $publishableStatus = 'publish_status';

    protected $publishablePublishedAt = 'publish_at';

    protected $fillable = [
        'id',
        'title',
        'uuid',
        'slug_sort',
        'slug_custom',
        'contributor',
        'description_custom',
        'year',
        'released_on',
        'rights',
        'serie_id',
        'author',
        'author_id',
        'volume',
        'publisher_id',
        'language_slug',
        'page_count',
        'is_maturity_rating',
        'is_hidden',
        'type',
        'isbn',
        'isbn10',
        'isbn13',
        'identifiers',
        'google_book_id',
        'body_custom',
        'time_to_read_custom',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'year' => 'integer',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
