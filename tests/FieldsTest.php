<?php

namespace Kiwilan\Steward\Tests;

it('have package name', function () {
    /** @var TestCase $this */
    $fieldText = (string) $this->blade('<x-stw-field-text />');
    expect($fieldText)->toBeString();
});
