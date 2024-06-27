<?php

namespace Kiwilan\Steward\Tests\Views;

it('can render blade app', function () {
    $rendered = (string) blade('<x-stw-app />');
    expect($rendered)->toBeString();
});

it('can render button', function () {
    $rendered = (string) blade('<x-stw-button />');
    expect($rendered)->toBeString();
});

it('can render color mode', function () {
    $rendered = (string) blade('<x-stw-color-mode />');
    expect($rendered)->toBeString();
});

it('can render field checkbox', function () {
    $rendered = (string) blade('<x-stw-field.checkbox />');
    expect($rendered)->toBeString();
});

it('can render field rich editor', function () {
    $rendered = (string) blade('<x-stw-field.rich-editor />');
    expect($rendered)->toBeString();
});

it('can render field select', function () {
    $rendered = (string) blade('<x-stw-field.select />');
    expect($rendered)->toBeString();
});

it('can render field text', function () {
    $rendered = (string) blade('<x-stw-field.text />');
    expect($rendered)->toBeString();
});

it('can render field toggle', function () {
    $rendered = (string) blade('<x-stw-field.toggle />');
    expect($rendered)->toBeString();
});

it('can render field upload file', function () {
    $rendered = (string) blade('<x-stw-field.upload-file />');
    expect($rendered)->toBeString();
});

// it('can render listing', function () {
//     $rendered = (string) blade('<x-stw-listing />');
//     expect($rendered)->toBeString();
// });

// it('can render listing filters', function () {
//     $rendered = (string) blade('<x-stw-listing.filters />');
//     expect($rendered)->toBeString();
// });

// it('can render listing filters mobile', function () {
//     $rendered = (string) blade('<x-stw-listing.filters-mobile />');
//     expect($rendered)->toBeString();
// });

it('can render listing pagination', function () {
    $rendered = (string) blade('<x-stw-listing.pagination />');
    expect($rendered)->toBeString();
});

it('can render listing pagination size', function () {
    $rendered = (string) blade('<x-stw-listing.pagination-size />');
    expect($rendered)->toBeString();
});

it('can render listing search', function () {
    $rendered = (string) blade('<x-stw-listing.search />');
    expect($rendered)->toBeString();
});

it('can render listing sorters', function () {
    $rendered = (string) blade('<x-stw-listing.sorters />');
    expect($rendered)->toBeString();
});

// it('can render head meta', function () {
//     $rendered = (string) blade('<x-stw-head-meta />');
//     expect($rendered)->toBeString();
// });
