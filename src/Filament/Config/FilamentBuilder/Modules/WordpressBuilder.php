<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder\Modules;

use Closure;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Kiwilan\Steward\Enums\SocialEnum;
use Kiwilan\Steward\Filament\Config\FilamentBuilder;
use Kiwilan\Steward\Filament\Config\FilamentBuilder\Faker\WordpressBuilderFaker;
use Kiwilan\Steward\Filament\Config\FilamentBuilder\FilamentBuilderModule;

class WordpressBuilder implements FilamentBuilderModule
{
    public const NAME = 'wordpress';

    public const FAKER = WordpressBuilderFaker::class;

    public static function make(): array
    {
        // paragraph => editor
        // codeBlock => markdown
        // gallery => mosaic, masonry
        // fichier
        // audio
        // button
        // divider
        // toggle toc
        // toggle share
        // share post
        // share swiper posts
        // blockquote => optional
        // toggle comments
        // embedded => twitter, youtube, soundcloud, spotify, flickr, vimeo, dailymotion, imgur, kickstarter, pocket casts, reddit, tiktok, ted, tumblr, pinterest, facebook, insta, insta feed, gif, pinterest, podcast player, same article, twitch, snapchat
        // google map
        // sondage
        // money: don, paypal

        return [
            WordpressBuilder::heading(),
            WordpressBuilder::richParagraph(),
            WordpressBuilder::paragraph(),
            WordpressBuilder::image(),
            WordpressBuilder::embedded(),
            WordpressBuilder::codeBlock(),
            WordpressBuilder::gallery(),
            WordpressBuilder::button(),
            WordpressBuilder::spacer(),
            WordpressBuilder::divider(),
            WordpressBuilder::html(),
            // WordpressBuilder::embed(),
            // WordpressBuilder::shortcode(),
            // WordpressBuilder::table(),
            // WordpressBuilder::accordion(),
            // WordpressBuilder::tabs(),
            WordpressBuilder::alert(),
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
                    '2' => 'Heading 2',
                    '3' => 'Heading 3',
                    '4' => 'Heading 4',
                    '5' => 'Heading 5',
                    '6' => 'Heading 6',
                ])
                ->default('2')
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
            ->icon('heroicon-o-bars-3-bottom-left')
            ->get();
    }

    public static function richParagraph(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\RichEditor::make('rich_paragraph')
                ->label('Rich Paragraph')
                ->columnSpan(2)
                ->required()
                ->fileAttachmentsDirectory('attachments'),
        ])
            ->name('rich_paragraph')
            ->icon('heroicon-s-bars-arrow-up')
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
                ->columnSpan(2),
        ])
            ->name('image')
            ->icon('heroicon-o-photo')
            ->get();
    }

    public static function embedded(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\Placeholder::make('helper')
                ->label('You can use URL `https://www.youtube.com/watch?v=aqz-KE-bpKQ` or ID `aqz-KE-bpKQ`, short link can works too `https://youtu.be/aqz-KE-bpKQ`.')
                ->columnSpan(2),
            Forms\Components\TextInput::make('url')
                ->label('URL of the media')
                ->name('url')
                ->placeholder('https://www.example.com/media-id')
                ->helperText('Set URL of the media you want to embed.')
                ->columnSpan(2),
            // ->reactive()
            // ->afterStateUpdated(function (Closure $set, $state) {
            //     $enum = SocialEnum::find($state);

            //     if ($enum) {
            //         $set('origin', $enum->value);
            //     }
            // })
            // ->required(),
            // Forms\Components\Select::make('type')
            //     ->options(SocialEnum::toArray())
            //     ->helperText('Select the website of your media.')
            //     ->columnSpan(2)
            //     ->required(),
        ])
            ->name('embedded_media')
            ->icon('heroicon-o-camera')
            ->get();
    }

    public static function codeBlock(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\MarkdownEditor::make('code_block')
                ->label('Editor')
                ->toolbarButtons([
                    'codeBlock',
                ])
                ->columnSpan(2)
                ->required(),
        ])
            ->name('code_block')
            ->icon('heroicon-o-code-bracket-square')
            ->get();
    }

    public static function gallery(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\FileUpload::make('gallery')
                ->label('Gallery')
                ->multiple()
                ->directory('attachments')
                ->columnSpan(2)
                ->required(),
        ])
            ->name('gallery')
            ->icon('heroicon-o-camera')
            ->get();
    }

    public static function divider(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\Placeholder::make('divider')
                ->label('A divider is a line that separates content in your post.')
                ->columnSpan(2),
        ])
            ->name('divider')
            ->icon('heroicon-o-minus')
            ->get();
    }

    public static function button(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\TextInput::make('button')
                ->label('Button')
                ->columnSpan(2)
                ->required(),
            Forms\Components\TextInput::make('url')
                ->label('URL')
                ->columnSpan(2)
                ->required(),
        ])
            ->name('button')
            ->icon('heroicon-o-cursor-arrow-rays')
            ->get();
    }

    public static function spacer(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\Select::make('spacer')
                ->label('Spacer')
                ->options([
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large',
                ])
                ->default('medium')
                ->columnSpan(2)
                ->required(),
        ])
            ->name('spacer')
            ->icon('heroicon-o-arrows-up-down')
            ->get();
    }

    public static function alert(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\Select::make('type')
                ->label('Type')
                ->options([
                    'info' => 'Info',
                    'success' => 'Success',
                    'warning' => 'Warning',
                    'danger' => 'Danger',
                ])
                ->default('info')
                ->columnSpan(2)
                ->required(),
            Forms\Components\RichEditor::make('alert')
                ->label('Alert')
                ->toolbarButtons([
                    'bold',
                    'italic',
                    'link',
                    'redo',
                    'strike',
                    'undo',
                ])
                ->columnSpan(2)
                ->required(),
        ])
            ->name('alert')
            ->icon('heroicon-o-exclamation-triangle')
            ->get();
    }

    public static function html(): Block
    {
        return FilamentBuilder::block([
            Forms\Components\Textarea::make('html')
                ->label('HTML')
                ->columnSpan(2)
                ->required(),
        ])
            ->name('html')
            ->icon('heroicon-o-document')
            ->get();
    }
}
