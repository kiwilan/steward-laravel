<?php

use Kiwilan\Steward\Services\GoogleBookService;

it('can use service', function () {
    $service = GoogleBookService::make(booksIsbn())
        ->setIsbnFields(['isbn10', 'isbn13'])
        ->execute()
    ;

    expect($service->getItems()->first()->getDescription())->toBeString();
});
