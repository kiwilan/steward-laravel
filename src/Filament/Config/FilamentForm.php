<?php

namespace Kiwilan\Steward\Filament\Config;

use Closure;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Kiwilan\Steward\Enums\MediaTypeEnum;
use Kiwilan\Steward\Enums\UserRoleEnum;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutCard;

class FilamentForm
{
    /**
     * Generate metalink and meta title from current field.
     *
     * @param  string  $field It will be `name` by default.
     * @param  string|false  $metaLink It will be `slug` by default.
     * @param  string|false  $metaTitle
     * @param  string  $label
     * @param  string|null  $helper
     * @param  string  $skipContext
     * @param  int  $width
     * @param  bool  $required
     */
    public static function name(
        string $field = 'name',
        string|false $metaLink = 'slug',
        string|false $metaTitle = 'meta_title',
        string $label = 'Name',
        string $helper = null,
        string $skipContext = 'edit',
        int $width = 1,
        bool $required = true,
    ) {
        if (null === $helper) {
            $transGenerate = __('steward::filament.form_helper.generate');

            $transMetaLink = ! $metaLink ? __('steward::filament.form_helper.metalink') : '';
            $transMetaField = ! $metaTitle ? __('steward::filament.form_helper.meta_title') : '';
            $twin = [$transMetaLink, $transMetaField];
            $twin = array_filter($twin);
            $twin = implode(', ', $twin);

            $onlyOn = __('steward::filament.form_helper.only_on');
            $context = $skipContext === 'edit' ? 'create' : 'edit';
            $context = __("steward::filament.form_helper.{$context}");
            $helper = "{$transGenerate} {$twin} {$onlyOn} {$context}.";
        }

        return Forms\Components\TextInput::make($field)
            ->label($label)
            ->helperText($helper)
            ->required($required)
            ->reactive()
            ->afterStateUpdated(function (string $context, Closure $set, $state) use ($metaLink, $metaTitle, $skipContext) {
                if ($skipContext === $context) {
                    return;
                }

                if ($metaLink) {
                    $set($metaLink, Str::slug($state));
                }

                if ($metaTitle) {
                    $set($metaTitle, $state);
                }
            })
            ->columnSpan($width);
    }

    public static function description(
        string $field = 'description',
        string|false $metaField = 'meta_description',
        string $label = 'Description',
        string $helper = null,
        string $skipContext = 'edit',
        int $width = 1,
        bool $required = false,
    ) {
        if (null === $helper && $metaField) {
            $transGenerate = __('steward::filament.form_helper.generate');
            $transMetaField = __('steward::filament.form_helper.meta_description');
            $onlyOn = __('steward::filament.form_helper.only_on');
            $context = $skipContext === 'edit' ? 'create' : 'edit';
            $context = __("steward::filament.form_helper.{$context}");
            $helper = "{$transGenerate} {$transMetaField} {$onlyOn} {$context}.";
        }

        return Forms\Components\Textarea::make($field)
            ->label($label)
            ->helperText($helper)
            ->required($required)
            ->reactive()
            ->afterStateUpdated(function (string $context, Closure $set, $state) use ($skipContext, $metaField) {
                if ($skipContext === $context) {
                    return;
                }

                if ($metaField) {
                    $set($metaField, $state);
                }
            })
            ->columnSpan($width);
    }

    /**
     * @param  string  $current_action 'create' or 'edit'
     * @return Closure
     */
    public static function disabledOn(string $current_action)
    {
        return function (mixed $livewire) use ($current_action) {
            $class = get_class($livewire);
            $class = explode('\\', $class);
            $action = $class[count($class) - 1];

            if (str_contains(strtolower($action), $current_action)) {
                return true;
            }
        };
    }

    /**
     * Update field on context type.
     *
     * @param  "create"|"edit"  $context_type
     * @return Closure
     */
    public static function afterStateUpdated(string|array $field, string $context_type = 'edit')
    {
        return function (string $context, Closure $set, $state) use ($field, $context_type) {
            if ($context === $context_type) {
                return;
            }

            if (is_array($field)) {
                foreach ($field as $item) {
                    $set($item, $state);
                }
            } else {
                $set($field, $state);
            }
        };
    }

    public static function meta(bool $card = false)
    {
        $timestamps = [
            Forms\Components\Placeholder::make('id')
                ->label('ID')
                ->content(fn ($record): ?string => $record?->id),
            Forms\Components\Placeholder::make('created_at')
                ->label('Created at')
                ->content(fn ($record): ?string => $record?->created_at?->diffForHumans()),
            Forms\Components\Placeholder::make('updated_at')
                ->label('Updated at')
                ->content(fn ($record): ?string => $record?->updated_at?->diffForHumans()),
        ];

        return $card ? FilamentLayoutCard::make($timestamps, 'Timestamps') : Forms\Components\Group::make($timestamps);
    }

    public static function seo(bool $card = false)
    {
        $seo = [
            Forms\Components\Placeholder::make('seo')
                ->label('SEO'),
            Forms\Components\TextInput::make('slug')
                ->label('Metalien')
                ->required()
                ->unique(column: 'slug', ignoreRecord: true),
            Forms\Components\TextInput::make('meta_title')
                ->label('Titre'),
            Forms\Components\Textarea::make('meta_description')
                ->label('Description'),
        ];

        return $card ? FilamentLayoutCard::make($seo, 'SEO') : Forms\Components\Group::make($seo);
    }

    public static function dateFilter(string $field = 'created_at')
    {
        return Filter::make('created_at')
            ->form([
                Forms\Components\DatePicker::make('created_from')
                    ->label('Publié depuis le')
                    ->placeholder(fn ($state): string => now()->subYear()->format('M d, Y')),
                Forms\Components\DatePicker::make('created_until')
                    ->label("Publié jusqu'au")
                    ->placeholder(fn ($state): string => now()->format('M d, Y')),
            ])
            ->query(
                fn (Builder $query, array $data): Builder => $query
                    ->when(
                        $data['created_from'],
                        fn (Builder $query, $date): Builder => $query->whereDate($field, '>=', $date),
                    )
                    ->when(
                        $data['created_until'],
                        fn (Builder $query, $date): Builder => $query->whereDate($field, '<=', $date),
                    )
            );
    }

    public static function checkRole(UserRoleEnum $role = UserRoleEnum::super_admin)
    {
        return function () use ($role) {
            /** @var object $user */
            $user = auth()->user();

            if (property_exists($user, 'role')) {
                return $user->role?->value !== $role->value;
            }

            return false;
        };
    }

    public static function pictureField(
        string $field = 'picture',
        string $label = 'Picture',
        MediaTypeEnum $type = MediaTypeEnum::media,
        array $fileTypes = [
            'image/jpeg',
            'image/webp',
            'image/png',
            'image/svg+xml',
        ],
        string $hint = 'Accepte JPG, WEBP, PNG, SVG'
    ) {
        return Components\FileUpload::make($field)
            ->label($label)
            ->hint($hint)
            ->acceptedFileTypes($fileTypes)
            ->image()
            ->maxSize(1024)
            ->directory($type->name);
    }

    public static function display()
    {
        return Forms\Components\Toggle::make('display')
            ->helperText('Show this block on the page')
            ->label('Display')
            ->default(true)
            ->columnSpan(2);
    }

    public static function showAction()
    {
        return Action::make('show')
            ->url(fn ($record): string => "{$record->show_live}")
            ->icon('heroicon-o-eye')
            ->openUrlInNewTab()
            ->color('warning')
            ->label('Voir');
    }
}
