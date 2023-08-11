<?php

namespace Kiwilan\Steward\Tests\Views\Field;

it('have package name', function () {
    $renderedView = (string) $this->blade('<x-stw-field.text />');
    expect($renderedView)->toBeString();
});

it('can be render', function () {
    $view = $this->component(\Kiwilan\Steward\Components\Field\Text::class, [
        'label' => 'Name',
        'name' => 'name',
        'type' => 'text',
    ]);
    $view->assertSee('Name');
    $view->assertSee('name');
    $view->assertSee('text');
});
