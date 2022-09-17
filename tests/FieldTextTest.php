<?php

namespace Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Kiwilan\Steward\Components\Button;
use Kiwilan\Steward\Components\FieldText;
use Kiwilan\Steward\Tests\TestCase;

class FieldTextTest extends TestCase
{
    use InteractsWithViews;

    /** @test */
    public function can_be_render()
    {
        $view = $this->component(FieldText::class, [
            'label' => 'Name',
            'name' => 'name',
            'type' => 'text',
        ]);
        $view->assertSee('Name');
        $view->assertSee('name');
        $view->assertSee('text');
    }
}
