<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder;

use Filament\Forms;
use Kiwilan\Steward\Enums\BuilderEnum\BuilderWordpressVideoEnum;

class WordpressBuilder implements IFilamentBuilder
{
    public static function make(): array
    {
        return [
            WordpressBuilder::heading(),
            WordpressBuilder::paragraph(),
            WordpressBuilder::image(),
            WordpressBuilder::video(),
            // WordpressBuilder::gallery(),
            // WordpressBuilder::button(),
            // WordpressBuilder::spacer(),
            // WordpressBuilder::divider(),
            // WordpressBuilder::html(),
            // WordpressBuilder::code(),
            // WordpressBuilder::embed(),
            // WordpressBuilder::shortcode(),
            // WordpressBuilder::list(),
            // WordpressBuilder::table(),
            // WordpressBuilder::accordion(),
            // WordpressBuilder::tabs(),
            // WordpressBuilder::alert(),
        ];
    }

    public static function heading()
    {
        return HelperBuilder::block([
            Forms\Components\TextInput::make('heading')
                ->label('Heading')
                ->required(),
            Forms\Components\Select::make('level')
                ->options([
                    'h1' => 'Heading 1',
                    'h2' => 'Heading 2',
                    'h3' => 'Heading 3',
                    'h4' => 'Heading 4',
                    'h5' => 'Heading 5',
                    'h6' => 'Heading 6',
                ])
                ->required(),
        ])
            ->name('heading')
            ->icon('heroicon-o-bookmark')
            ->get()
        ;
    }

    public static function paragraph()
    {
        return HelperBuilder::block([
            Forms\Components\RichEditor::make('paragraph')
                ->label('Paragraph')
                ->toolbarButtons([
                    'blockquote',
                    'bold',
                    'bulletList',
                    'italic',
                    'link',
                    'orderedList',
                    'redo',
                    'strike',
                    'undo',
                ])
                ->columnSpan(2)
                ->required(),
        ])
            ->name('paragraph')
            ->icon('heroicon-o-menu')
            ->get()
        ;
    }

    public static function image()
    {
        return HelperBuilder::block([
            Forms\Components\FileUpload::make('image')
                ->label('Image')
                ->columnSpan(2)
                ->required(),
            Forms\Components\TextInput::make('alt')
                ->label('Alt Text')
                ->columnSpan(2)
                ->required(),
        ])
            ->name('image')
            ->icon('heroicon-o-photograph')
            ->get()
        ;
    }

    public static function video()
    {
        return HelperBuilder::block([
            Forms\Components\TextInput::make('id')
                ->label('Video ID')
                ->required(),
            Forms\Components\Select::make('type')
                ->options(BuilderWordpressVideoEnum::toArray())
                ->default(BuilderWordpressVideoEnum::youtube->value)
                ->required(),
        ])
            ->name('image')
            ->icon('heroicon-o-video-camera')
            ->get()
        ;
    }
}
