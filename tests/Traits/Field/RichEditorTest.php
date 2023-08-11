<?php

namespace Kiwilan\Steward\Tests\Views\Field;

use Kiwilan\Steward\Tests\TestCase;

it('have package name', function () {
    /** @var TestCase $this */
    $renderedView = (string) $this->blade('<x-stw-field.rich-editor
        name="biography"
        label="Biography"
        wire:model="biography"
    />');
    expect($renderedView)->toBeString();
});

it('can be render', function () {
    /** @var TestCase $this */
    $view = $this->component(\Kiwilan\Steward\Components\Field\RichEditor::class, [
        'label' => 'Editor',
    ]);
    $view->assertSee('Editor');
});
