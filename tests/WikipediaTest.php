<?php

use Kiwilan\Steward\Utils\Wikipedia;

it('can use service', function () {
    $wikipedia = Wikipedia::make('pierre bottero')
        ->language('fr')
        ->withImage()
        ->get();

    expect($wikipedia->getItem()->getRequestUrl())->toBe('http://fr.wikipedia.org/w/api.php?action=query&pageids=1064109&inprop=url&format=json&prop=info%7Cextracts%7Cpageimages&pithumbsize=512');
    expect($wikipedia->getItem()->getPageId())->toBeInt();
    expect($wikipedia->getItem()->getLanguage())->toBe('fr');
    expect($wikipedia->getItem()->getTitle())->toBe('Pierre Bottero');
    expect($wikipedia->getItem()->getFullUrl())->toBe('https://fr.wikipedia.org/wiki/Pierre_Bottero');
    expect($wikipedia->getItem()->getWordCount())->toBeInt();
    expect($wikipedia->getItem()->getTimestamp())->toBeInstanceOf(DateTime::class);
    expect($wikipedia->getItem()->getExtract())->toBeString();
    expect($wikipedia->getItem()->getFullText())->toBeString();
    expect($wikipedia->getItem()->getPictureUrl())->toEndWith('jpg');
    expect($wikipedia->getItem()->getPictureBase64())->toBeString();

    $wikipedia = Wikipedia::make('asp explorer')
        ->language('fr')
        ->precision(['communauté'])
        ->get();
    expect($wikipedia->getItem())->not()->toBeNull();

    $wikipedia = Wikipedia::make('Adeyemi Tomi')->get();
    expect($wikipedia->getItem())->not()->toBeNull();

    $wikipedia = Wikipedia::make('Gemmell Stella')
        ->exact()
        ->get();
    expect($wikipedia->getItem())->toBeNull();

    $wikipedia = Wikipedia::make('Guin Ursula Le')
        ->exact()
        ->get();
    expect($wikipedia->getItem())->toBeNull();

    $wikipedia = Wikipedia::make('Ursula K. Le Guin')
        ->exact()
        ->get();
    expect($wikipedia->getItem())->not()->toBeNull();
});
