<?php

namespace Kiwilan\Steward\Filament\Config;

use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Illuminate\Support\Str;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutContainer;

class FilamentLayout
{
    public function __construct(
        public int $width = 3,
        public ?Form $form = null,
        public array $columns = [],
        public mixed $layout = null,
    ) {
    }

    public static function make(Form $form, mixed $columns = null): Form
    {
        // return FilamentLayoutContainer::make($columns);
        $filament = new FilamentLayout();
        // $form = new Form();

        // $filament->columns = $columns;
        $filament->form = $form->columns($columns);

        // $filament = $form
        // ->schema($columns)
        // ->columns([
        //     'sm' => $this->width,
        //     'lg' => null,
        // ]);

        return $filament->form;
    }

    public function form(Form $form): Form
    {
        $this->form = $form;

        return $this->form;
    }

    public function width(int $width = 3): Form
    {
        $this->width = $width;

        return $this->form;
    }

    public static function column(array|Closure $content = [], int $width = 2)
    {
        $parts = [];
        foreach ($content as $part) {
            if (! empty($part)) {
                $parts[] = Forms\Components\Card::make()
                    ->schema($part)
                    ->columns([
                        'sm' => $width,
                    ]);
            } else {
                $parts[] = Forms\Components\Group::make();
            }
        }

        return Forms\Components\Group::make()
            ->schema($parts)
            ->columnSpan([
                'sm' => $width,
            ]);
    }

    public static function card(array|Closure $card = [], int $columns = 2, string $title = '')
    {
        return Forms\Components\Card::make()
            ->schema([
                Forms\Components\Placeholder::make(Str::slug($title))
                    ->label($title)
                    ->columnSpan(2),
                ...$card,
            ])
            ->columns($columns);
    }
}
