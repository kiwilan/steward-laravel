<?php

use Kiwilan\Steward\Utils\InternetAccess;

it('can check if internet is available', function () {
    $check = InternetAccess::check();

    expect($check->isAvailable())->toBeTrue();
    expect($check->isOk())->toBeTrue();
});

it('can check with https', function () {
    $check = InternetAccess::check(force_https: true);

    expect($check->isAvailable())->toBeTrue();
});

it('can check with http prefix', function () {
    $check = InternetAccess::check('http://www.google.com');

    expect($check->isAvailable())->toBeTrue();
});

it('can check with ewilan-riviere.com', function () {
    $check = InternetAccess::check('ewilan-riviere.com');

    expect($check->isAvailable())->toBeTrue();
});

it('can check with abcdefghikl.com', function () {
    $check = InternetAccess::check('abcdefghikl.com');

    expect($check->isAvailable())->toBeFalse();
});
