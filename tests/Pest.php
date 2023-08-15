<?php

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Testing\TestView;
use Kiwilan\Steward\Tests\Data\Models\Author;
use Kiwilan\Steward\Tests\Data\Models\Book;
use Kiwilan\Steward\Tests\TestCase;

// uses(TestCase::class)->in(__DIR__);
uses(TestCase::class, InteractsWithViews::class)->in('.');

function dotenv(): array
{
    $path = __DIR__.'/../';
    $lines = file($path.'.env');
    $dotenv = [];

    foreach ($lines as $line) {
        if (! empty($line)) {
            $data = explode('=', $line);
            $key = $data[0];
            $value = $data[1];

            $key = trim($key);
            $value = trim($value);

            $dotenv[$key] = $value;
        }
    }

    return $dotenv;
}

/**
 * Render the contents of the given Blade template string.
 *
 * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
 * @return \Illuminate\Testing\TestView
 */
function blade(string $template, $data = [])
{
    $tempDirectory = sys_get_temp_dir();

    $finder = ViewFacade::getFinder();
    $list = [];

    if (method_exists($finder, 'getPaths')) {
        /** @var mixed $finder */
        $list = $finder->getPaths();
    }

    if (! in_array($tempDirectory, $list)) {
        ViewFacade::addLocation(sys_get_temp_dir());
    }

    $tempFileInfo = pathinfo(tempnam($tempDirectory, 'laravel-blade'));

    $tempFile = $tempFileInfo['dirname'].'/'.$tempFileInfo['filename'].'.blade.php';

    file_put_contents($tempFile, $template);

    return new TestView(view($tempFileInfo['filename'], $data));
}

function bookTitle(): string
{
    return 'The Lord of the Rings';
}

function bookDescription(): string
{
    return 'The Lord of the Rings is an epic high-fantasy novel written by English author and scholar J. R. R. Tolkien.';
}

function book(): Book
{
    return Book::create([
        'title' => 'The Lord of the Rings',
        'author' => 'J. R. R. Tolkien',
        'year' => 1954,
        'description_custom' => 'The Lord of the Rings is an epic high-fantasy novel written by English author and scholar J. R. R. Tolkien.',
    ]);
}

/**
 * @return Collection<stdClass>
 */
function books(): Collection
{
    $items = collect();

    $json = file_get_contents(__DIR__.'/Data/books.json');
    $data = json_decode($json, true);

    foreach ($data as $item) {
        $class = new stdClass();
        $class->title = $item['title'];
        $class->language_slug = $item['language_slug'];
        $items->push($class);
    }

    return $items->splice(0, 25);
}

/**
 * @return Collection<stdClass>
 */
function series(): Collection
{
    $items = collect();

    $json = file_get_contents(__DIR__.'/Data/series.json');
    $data = json_decode($json, true);

    foreach ($data as $item) {
        $class = new stdClass();
        $class->title = $item['title'];
        $class->language_slug = $item['language_slug'];
        $items->push($class);
    }

    return $items->splice(0, 25);
}

/**
 * @return Collection<Book>
 */
function booksModel(): Collection
{
    $items = collect();

    $json = file_get_contents(__DIR__.'/Data/books.json');
    $data = json_decode($json, true);

    $id = 0;

    foreach ($data as $item) {
        $id++;
        $book = new Book();
        $book->id = $id;
        $book->title = $item['title'];
        $book->language_slug = $item['language_slug'];
        $items->push($book);
    }

    return $items->splice(0, 25);
}

/**
 * @return Collection<Book>
 */
function booksIsbn(): Collection
{
    $items = collect();

    $json = file_get_contents(__DIR__.'/Data/books-isbn.json');
    $data = json_decode($json, true);

    $id = 0;

    foreach ($data as $item) {
        $id++;
        $class = new stdClass();
        $class->id = $id;
        $class->title = $item['title'];
        $class->isbn10 = $item['isbn10'];
        $class->isbn13 = $item['isbn13'];
        $items->push($class);
    }

    return $items->splice(0, 25);
}

/**
 * @return Collection<Book>
 */
function insertBooksList(): Collection
{
    Book::truncate();
    Author::truncate();
    $books = collect();

    $json = file_get_contents(__DIR__.'/Data/books-full.json');
    $data = json_decode($json, true);

    Author::create([
        'name' => 'J. R. R. Tolkien',
    ]);

    Author::create([
        'name' => 'Frank Herbert',
    ]);

    foreach ($data as $item) {
        $book = Book::create($item);
        $book->publish_status = 'published';
        $book->publish_at = now();
        $book->save();

        $books->push($book);
    }

    $first = $books->first();
    $first->author_id = 1;
    $first->publish_status = 'scheduled';
    $first->save();

    $last = $books->last();
    $last->author_id = 2;
    $last->publish_status = 'draft';
    $last->save();

    return $books;
}

function setRequest(
    string $uri,
    string $method = 'GET',
    array $parameters = [],
    array $cookies = [],
    array $files = [],
    array $server = ['CONTENT_TYPE' => 'application/json'],
    mixed $content = null,
) {
    $request = new \Illuminate\Http\Request();

    return $request->createFromBase(
        \Symfony\Component\HttpFoundation\Request::create(
            $uri,
            $method,
            $parameters,
            $cookies,
            $files,
            $server,
            $content
        )
    );
}

function listExports()
{
    $folder_path = './tests/exports';
    $allowed_extensions = ['csv', 'xls', 'xlsx'];

    $files = [];

    foreach ($allowed_extensions as $extension) {
        $files = array_merge($files, glob("{$folder_path}/*.{$extension}"));
    }

    return $files;
}

function clearExports()
{
    $files = listExports();

    foreach ($files as $file) {
        unlink($file);
    }
}
