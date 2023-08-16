<?php

namespace Kiwilan\Steward\Tests\Data\Exports;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Lang;
use Kiwilan\Steward\Tests\Data\Models\Book;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\QueryBuilder\QueryBuilder;

class BookExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    private Builder|QueryBuilder $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return collect([
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
        ])
            ->map(
                fn ($field) => Lang::has("crud.books.attributes.{$field}")
                    ? __("crud.books.attributes.{$field}")
                    : __("admin.attributes.{$field}")
            )->toArray()
        ;
    }

    /**
     * @param  Book  $row
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->title,
            $row->uuid,
            $row->slug_sort,
            $row->slug_custom,
            $row->contributor,
            $row->description_custom,
            $row->year,
            $row->released_on,
            $row->rights,
            $row->serie_id,
            $row->author,
            $row->author_id,
            $row->volume,
            $row->publisher_id,
            $row->language_slug,
            $row->page_count,
            $row->is_maturity_rating,
            $row->is_hidden,
            $row->type,
            $row->isbn,
            $row->isbn10,
            $row->isbn13,
            $row->identifiers,
            $row->google_book_id,
            $row->body_custom,
            $row->time_to_read_custom,
            $row->created_at,
            $row->updated_at,
        ];
    }
}
