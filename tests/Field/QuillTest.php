<?php

namespace Tests\Field;

use Kiwilan\Steward\Components\Field\FieldQuill;
use Kiwilan\Steward\Tests\TestCase;

it('have package name', function () {
    /** @var TestCase $this */
    $renderedView = (string) $this->blade('<x-stw-field-quill
        name="biography"
        label="Biography"
        wire:model="biography"
    />');
    expect($renderedView)->toBeString();
});

it('can be render', function () {
    /** @var TestCase $this */
    $view = $this->component(FieldQuill::class, [
        'label' => 'Editor',
    ]);
    $view->assertSee('Editor');
});
