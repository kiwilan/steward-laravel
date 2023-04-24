<?php

namespace Kiwilan\Steward\Tests\Views;

use Kiwilan\Steward\Components\Button;
use Mockery\MockInterface;
use function Pest\Laravel\partialMock;

it('have package name', function () {
    /** @var TestCase $this */
    $renderedView = (string) $this->blade('<x-stw-button />');
    expect($renderedView)->toBeString();
});

it('can be button', function () {
    /** @var TestCase $this */
    $renderedView = (string) $this->blade(
        <<<'BLADE'
        <x-stw-button type="button">
            Submit
        </x-stw-button>
        BLADE
    );

    expect($renderedView)->toBeString();
    expect($renderedView)->toMatch('/type="button"/');
});

it('can be link', function () {
    /** @var TestCase $this */
    $view = $this->component(Button::class, [
        'href' => '/',
    ]);

    $view->assertSee('href');
    $view->assertDontSee('_blank');
});

it('can be external link', function () {
    /** @var TestCase $this */
    $view = $this->component(Button::class, [
        'href' => '/',
        'external' => true,
    ]);

    $view->assertSee('href');
    $view->assertSee('_blank');
});

it('can be render', function () {
    partialMock(Button::class, function (MockInterface $button) {
        // $button->shouldReceive('type')->once();
        $button->shouldReceive('render');
        $button->shouldReceive([
            'type' => 'button',
            'href' => null,
            'external' => 'false',
            'slot' => null,
        ]);
        $button->allows('slot')->andReturn('Submit');

        // expect($button)->toHaveProperties(['type', 'href']);
        expect($button)->toHaveProperty('type');
    });
});

it('can have slot', function () {
    partialMock(Button::class, function (MockInterface $button) {
        $button->shouldReceive('render');
        $button->allows('slot')->andReturn('Submit');
        expect($button)->toHaveProperty('type');
    });
});

// test('guest can see homepage', function () {
//     Page::factory()->active()->home()->create([
//         'slug' => '/',
//     ]);
//     get('/')->assertStatus(200)->assertSee('Text');
// });
