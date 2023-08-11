<?php

use Kiwilan\Steward\Services\WikipediaService;

it('can use service', function () {
    $service = WikipediaService::make(series())
        ->setLanguageAttribute('language_slug')
        ->setQueryAttributes(['title'])
        ->setPrecisionQuery(['book', 'livre', 'série'])
        ->execute()
    ;

    expect($service->getItems()->first()->getTitle())->toBeString();
});

it('can use service with books', function () {
    $service = WikipediaService::make(books())
        ->setLanguageAttribute('language_slug')
        ->setQueryAttributes(['title'])
        ->execute()
    ;

    expect($service->getItems()->first()->getTitle())->toBeString();
});

it('can use service with books model', function () {
    $service = WikipediaService::make(booksModel())
        ->setLanguageAttribute('language_slug')
        ->setQueryAttributes(['title'])
        ->execute()
    ;

    expect($service->getItems()->first()->getTitle())->toBeString();
});
