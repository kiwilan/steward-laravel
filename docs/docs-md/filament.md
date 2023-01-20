# Filament

## FilamentLayout

```php
<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Kiwilan\Steward\Filament\Config\FilamentForm;
use Kiwilan\Steward\Filament\Config\FilamentLayout;

class StoryResource extends Resource
{
    public static function form(Form $form): Form
    {
        return FilamentLayout::make($form, [
            FilamentLayout::column([
                [
                    Forms\Components\TextInput::make('title')
                        ->columnSpan(2),
                ],
                [
                    // ...
                ],
            ]),
            FilamentLayout::column([
                [
                    // ...
                ],
            ], 1),
        ]);
    }
}
```
