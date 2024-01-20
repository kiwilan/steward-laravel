<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kiwilan\Steward\Queries\HttpQuery;
use Kiwilan\Steward\Queries\QueryResponse;
use Kiwilan\Steward\Services\ClassParser\ClassParserItem;
use Kiwilan\Steward\Tests\Data\Exports\BookExport;
use Kiwilan\Steward\Tests\Data\Models\Author;
use Kiwilan\Steward\Tests\Data\Models\Book;
use Spatie\QueryBuilder\QueryBuilder;

beforeEach(function () {
    config(['query-builder.parameters.include' => 'include']);
    config(['query-builder.parameters.filter' => 'filter']);
    config(['query-builder.parameters.sort' => 'sort']);
    config(['query-builder.parameters.fields' => 'fields']);
    config(['query-builder.parameters.append' => 'append']);
    config(['query-builder.count_suffix' => 'Count']);
    config(['query-builder.disable_invalid_filter_query_exception' => false]);
    config(['query-builder.request_data_source' => 'query_string']);

    insertBooksList();
});

it('can use http query', function () {
    $request = setRequest('/test');

    $queryBuilder = HttpQuery::for(Book::query(), $request);
    $query = HttpQuery::for(Book::class, $request);

    expect($query->getClass())->toBe('Kiwilan\Steward\Tests\Data\Models\Book');
    expect($queryBuilder->getClass())->toBe('Kiwilan\Steward\Tests\Data\Models\Book');

    expect($query->getInstance())->toBeInstanceOf(Book::class);
    expect($query->getBuilder())->toBeInstanceOf(Builder::class);
    expect($query->getParser())->toBeInstanceOf(ClassParserItem::class);
    expect($query->getRequest())->toBe($request);
    expect($query->getQuery())->toBeInstanceOf(QueryBuilder::class);

    $model = new Book();
    $opts = $query->getOptions();
    expect($opts['with'])->toBe($model->getQueryWith());
    expect($opts['withCount'])->toBe($model->getQueryWithCount());
    expect($opts['defaultSort'])->toBe($model->getQueryDefaultSort());
    expect($opts['allowFilters'])->toBe($model->getQueryAllowedFilters());
    expect($opts['allowSorts'])->toBe($model->getQueryAllowedSorts());
    expect($opts['export'])->toBe($model->getQueryExport());
    expect($opts['resource'])->toBe($model->getQueryResource());
    expect($opts['noPaginate'])->toBe($model->getQueryNoPaginate());
    expect($opts['pagination'])->toBe($model->getQueryPagination());
});

it('can use filters', function () {
    $request = setRequest('/books?filter[title]=Carbon');

    $query = HttpQuery::for(Book::class, $request);
    $res = $query->get();

    expect($res->count())->toBe(1);
});

it('can use sorters', function () {
    $request = setRequest('/books?sort=title');

    $query = HttpQuery::for(Book::class, $request);

    $res = $query->get();
    $first = $res->first();

    expect($first->slug_sort)->toBe('the-boys-07_faut-y-aller');
});

it('can use sorters reverse', function () {
    $request = setRequest('/books?sort=-title');
    $query = HttpQuery::for(Book::class, $request);

    $first = $query->get()->first();

    expect($first->slug_sort)->toBe('the-walking-dead-19_ezechiel');
});

it('can use advanced sorter', function () {
    $request = setRequest('/authors?sort=name');
    $query = HttpQuery::for(Author::class, $request);
    $res = $query->get();
    $first = $res->first();

    expect($first->name)->toBe('Frank Herbert');

    $request = setRequest('/authors?sort=-name-length');
    $query = HttpQuery::for(Author::class, $request);
    $res = $query->get();
    $first = $res->first();

    expect($first->name)->toBe('J. R. R. Tolkien');
});

it('can use advanced filter', function () {
    // filter custom `q` with `GlobalSearchFilter`
    $request = setRequest('/authors?filter[q]=tolkien');
    $query = HttpQuery::for(Author::class, $request);
    $res = $query->get();
    $first = $res->first();

    expect($first->name)->toBe('J. R. R. Tolkien');
    expect($res->count())->toBe(1);

    // filter exact `id`
    $request = setRequest('/authors?filter[id]=1');
    $query = HttpQuery::for(Author::class, $request);
    $res = $query->get();
    $first = $res->first();

    expect($first->name)->toBe('J. R. R. Tolkien');
    expect($res->count())->toBe(1);

    // filter partial `name`
    $request = setRequest('/authors?filter[name]=tolki');
    $query = HttpQuery::for(Author::class, $request);
    $res = $query->get();
    $first = $res->first();

    expect($first->name)->toBe('J. R. R. Tolkien');
    expect($res->count())->toBe(1);

    // filter callback relationship
    $request = setRequest('/authors?filter[books]=candide');
    $query = HttpQuery::for(Author::class, $request);
    $res = $query->get();
    $first = $res->first();

    expect($first->name)->toBe('Frank Herbert');
    expect($res->count())->toBe(1);
});

it('can use options', function () {
    $request = setRequest('/books?filter[uuid]=64d78bfeb54e2');
    $query = HttpQuery::for(Book::class, $request)
        ->filters(['uuid'])
        ->sorts(['uuid']);

    $res = $query->get();
    $first = $res->first();

    expect($first->slug_sort)->toBe('carbone-silicium');
});

it('can use http query api', function () {
    $request = setRequest('/test');

    $query = HttpQuery::for(Book::class, $request);
    $res = $query->paginate();
    $api = $query->collection();

    expect($res)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($api)->toBeInstanceOf(AnonymousResourceCollection::class);

    $query = HttpQuery::for(Author::class, $request);
    $res = $query->get();

    expect($res)->toBeInstanceOf(Collection::class);
});

it('can use front', function () {
    $request = setRequest('/test');

    $query = HttpQuery::for(Book::class, $request);
    $res = $query->front();

    expect($res)->toBeInstanceOf(QueryResponse::class);
});

it('can use default sorting', function () {
    $request = setRequest('/test');

    $query = HttpQuery::for(Book::class, $request);
    $first = $query->get()->first();

    expect($first->slug_sort)->toBe('20000-lieues-sous-les-mers');
});

it('can use export', function () {
    clearExports();
    $request = setRequest('/test');

    $query = HttpQuery::for(Book::class, $request);
    $path = getcwd().'/tests/exports';
    $exported = $query->export($path, true);

    $export = listExports();

    expect(count($export))->toBe(1);
    expect($exported)->toBe(true);
});

it('can use export excel', function () {
    clearExports();
    $request = setRequest('/test');

    $query = HttpQuery::for(Book::class, $request)
        ->exportable(BookExport::class);
    $path = getcwd().'/tests/exports';
    $exported = $query->export($path);

    $export = listExports();

    // expect(count($export))->toBe(1);
    expect($exported)->toBe(true);
});
