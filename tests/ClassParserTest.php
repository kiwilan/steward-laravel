<?php

use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\ClassParser\MetaClassItem;
use Kiwilan\Steward\Services\ClassParserService;
use Kiwilan\Steward\Tests\Data\Models\Book;
use Kiwilan\Steward\Traits\HasSlug;

it('can use class parser', function () {
    $parserClass = ClassParserService::make(Book::class);

    expect($parserClass->getName())->toBe('Book');
    expect($parserClass->getNamespace())->toBe('Kiwilan\Steward\Tests\Data\Models\Book');
    expect($parserClass->getExtends())->toBe('Illuminate\Database\Eloquent\Model');
    expect($parserClass->getInstance())->toBeInstanceOf(Book::class);
    expect($parserClass->getTraits())->toBeArray();
    expect($parserClass->getImplements())->toBeArray();
    expect($parserClass->getReflect())->toBeInstanceOf(ReflectionClass::class);
    expect($parserClass->isModel())->toBeTrue();
    expect($parserClass->getModel())->toBeInstanceOf(Book::class);

    expect($parserClass->useTrait(HasSlug::class))->toBeTrue();
    expect($parserClass->methodExists('getSlugColumn'))->toBeTrue();
    expect($parserClass->propertyExists('slugWith'))->toBeTrue();

    expect($parserClass->getMeta())->toBeInstanceOf(MetaClassItem::class);
});

it('can use class parser with path', function () {
    $path = getcwd().'/tests/Data/Models/Book.php';
    $parserPath = ClassParserService::make($path);

    expect($parserPath->getPath())->toBe($path);
    expect($parserPath->getFile())->toBeInstanceOf(SplFileInfo::class);
    expect($parserPath->getName())->toBe('Book');
    expect($parserPath->getNamespace())->toBe('Kiwilan\Steward\Tests\Data\Models\Book');
    expect($parserPath->getExtends())->toBe('Illuminate\Database\Eloquent\Model');
    expect($parserPath->isModel())->toBeTrue();
    expect($parserPath->getModel())->toBeInstanceOf(Book::class);
});

it('can use class parser with service', function () {
    $path = getcwd().'/tests/Data/Models';
    $items = ClassParserService::toCollection($path);

    expect($items)->toBeInstanceOf(Collection::class);
    expect($items->count())->toBe(1);
});

it('can use meta class', function () {
    $meta = MetaClassItem::make(Book::class);

    expect($meta->getClassString())->toBe(Book::class);
    expect($meta->getClassNamespaced())->toBe(Book::class);
    expect($meta->getClassName())->toBe('Book');
    expect($meta->getClassPlural())->toBe('Books');
    expect($meta->getClassSnake())->toBe('book');
    expect($meta->getClassSnakePlural())->toBe('books');
    expect($meta->getClassSlug())->toBe('book');
    expect($meta->getClassSlugPlural())->toBe('books');
    expect($meta->getFirstChar())->toBe('b');
});
