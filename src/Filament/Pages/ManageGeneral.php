<?php

namespace Kiwilan\Steward\Filament\Pages;

use Filament\Forms;
use Filament\Pages\SettingsPage;
use Kiwilan\Steward\Enums\BuilderEnum\BuilderSocialEnum;
use Kiwilan\Steward\Enums\LanguageEnum;
use Kiwilan\Steward\Filament\Config\FilamentBuilder\Generator\DateTimeZoneBuilder;
use Kiwilan\Steward\Filament\Config\FilamentLayout;
use Kiwilan\Steward\Jobs\ProcessFavicon;
use Kiwilan\Steward\Jobs\ProcessManifest;
use Kiwilan\Steward\Jobs\ProcessOpenGraph;
use Kiwilan\Steward\Settings\GeneralSettings;
use Livewire\TemporaryUploadedFile;

class ManageGeneral extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = GeneralSettings::class;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Website';

    protected function getFormSchema(): array
    {
        return [
            FilamentLayout::setting([
                Forms\Components\TextInput::make('site_name')
                    ->label('Site name')
                    ->required()
                    ->after(function () {
                        ProcessManifest::dispatch();
                    }),
                Forms\Components\Toggle::make('site_active')
                    ->label('Site active')
                    ->helperText('If the site is not active, it will be unavailable to the public.')
                    ->default(true)
                    ->required(),
                Forms\Components\TextInput::make('site_url')
                    ->label('Site URL')
                    ->helperText("Can't be changed here, contact the administrator.")
                    ->default(config('app.url'))
                    ->disabled(),
                Forms\Components\Select::make('site_lang')
                    ->label('Site language')
                    ->options(LanguageEnum::toArray())
                    ->default(LanguageEnum::en->value)
                    ->required(),
                Forms\Components\Select::make('site_utc')
                    ->label('Site UTC')
                    ->options(DateTimeZoneBuilder::make())
                    ->default('utc')
                    ->required(),
                Forms\Components\Textarea::make('site_description')
                    ->label('Site description')
                    ->columnSpan([
                        'sm' => 1,
                        'lg' => 2,
                    ]),
            ])->width(2)->title('General')->get(),
            FilamentLayout::setting([
                Forms\Components\FileUpload::make('site_favicon')
                    ->label('Site favicon')
                    ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/webp'])
                    ->maxSize(512)
                    ->disk('public')
                    ->directory('settings')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        $name = "favicon.{$file->getClientOriginalExtension()}";
                        ProcessFavicon::dispatch();

                        return $name;
                    }),
                Forms\Components\ColorPicker::make('site_color')
                    ->label('Site color')
                    ->default('#ffffff')
                    ->helperText("Defines the default theme color for the application. This sometimes affects how system displays the site (like on Android's task switcher, the theme color surrounds the site).")
                    ->required()
                    ->after(function () {
                        ProcessManifest::dispatch();
                    }),
                Forms\Components\FileUpload::make('default_image')
                    ->label('OpenGraph default image')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->maxSize(1024)
                    ->disk('public')
                    ->directory('settings')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        $name = "default.{$file->getClientOriginalExtension()}";
                        ProcessOpenGraph::dispatch();

                        return $name;
                    })
                    ->columnSpan([
                        'sm' => 1,
                        'lg' => 2,
                    ]),
            ])->width(2)->title('Theme')->get(),
            FilamentLayout::setting([
                Forms\Components\Repeater::make('social')
                    ->label('')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options(BuilderSocialEnum::toArray())
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('link')
                            ->url()
                            ->placeholder('https://example.com')
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columnSpan(2)
                    ->columns([
                        'sm' => 1,
                        'lg' => 2,
                    ]),
            ])
                ->title('Social')
                ->width(2)
                ->get(),
        ];
    }
}
