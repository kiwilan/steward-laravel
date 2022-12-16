<?php

namespace Tests\Field;

use Kiwilan\Steward\Components\Field\FieldText;
use Kiwilan\Steward\Tests\TestCase;

it('have package name', function () {
    /** @var TestCase $this */
    $renderedView = (string) $this->blade('<x-stw-field-text />');
    expect($renderedView)->toBeString();
});

it('can be render', function () {
    /** @var TestCase $this */
    $view = $this->component(FieldText::class, [
        'label' => 'Name',
        'name' => 'name',
        'type' => 'text',
    ]);
    $view->assertSee('Name');
    $view->assertSee('name');
    $view->assertSee('text');
});
