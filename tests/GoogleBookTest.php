<?php

use Kiwilan\Steward\Utils\GoogleBook;

it('can use service', function () {
    $googleBook = GoogleBook::make('9782329001371')->get();
    expect($googleBook->isAvailable())->toBeFalse();

    $googleBook = GoogleBook::make('9781448155217')->get();
    expect($googleBook->isAvailable())->toBeTrue();

    $googleBook = GoogleBook::make('9780486275437')
        ->identifier(1)
        ->get();
    expect($googleBook->isAvailable())->toBeTrue();
});
