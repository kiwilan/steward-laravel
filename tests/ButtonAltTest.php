<?php

use Kiwilan\Steward\Components\Button;
use Mockery\MockInterface;

use function Pest\Laravel\get;
use function Pest\Laravel\mock;
use function Pest\Laravel\partialMock;

it('can be render', function () {
    mock(Button::class, function (MockInterface $button) {
        // $button->shouldReceive('type')->once();
        // $button->shouldReceive('render');
        // $button->shouldReceive('render')
        //     ->once()
        //     ->andReturn('<x-stw-button>Submit</x-stw-button>');
        // dump($button->mockery_getMockableProperties());
        // expect($button)->toHaveProperties([
        // 'type' => 'button',
        // 'href' => null,
        // 'external' => 'false',
        // 'slot' => null
        // ]);
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
//     get('/')->assertStatus(200)->assertSee('Le groupe Domitys');
// });
