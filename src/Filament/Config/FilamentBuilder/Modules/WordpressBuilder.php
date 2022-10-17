<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder\Modules;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Kiwilan\Steward\Enums\BuilderEnum\BuilderVideoEnum;
use Kiwilan\Steward\Filament\Config\FilamentBuilder;
use Kiwilan\Steward\Filament\Config\FilamentBuilder\FilamentBuilderModule;

class WordpressBuilder implements FilamentBuilderModule
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

    public static function heading(): Block
    {
        return FilamentBuilder::block([
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
            ->get();
    }

    public static function paragraph(): Block
    {
        return FilamentBuilder::block([
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
            ->get();
    }

    public static function image(): Block
    {
        return FilamentBuilder::block([
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
            ->get();
    }

    public static function video(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\Placeholder::make('helper')
                ->label('You can use URL `https://www.youtube.com/watch?v=aqz-KE-bpKQ` or ID `aqz-KE-bpKQ`, short link can works too `https://youtu.be/aqz-KE-bpKQ`.')
                ->columnSpan(2),
            Forms\Components\TextInput::make('video')
                ->label('Video URL or ID')
                ->placeholder('https://www.youtube.com/watch?v=aqz-KE-bpKQ')
                ->helperText('Enter the URL or ID of the video, ID is the short code at the end of video.')
                ->columnSpan(2)
                ->required(),
            Forms\Components\Select::make('origin')
                ->options(BuilderVideoEnum::toArray())
                ->helperText("Select the origin of the video. If you don't know, try YouTube first.")
                ->columnSpan(2)
                ->required(),
            // TODO try to find the origin from video
        ])
            ->name('video')
            ->icon('heroicon-o-video-camera')
            ->get();
    }
}
