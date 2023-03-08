<?php

namespace Tests\Field;

use Kiwilan\Steward\Components\Field\FieldRichEditor;
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
    $view = $this->component(FieldRichEditor::class, [
        'label' => 'Editor',
    ]);
    $view->assertSee('Editor');
});
