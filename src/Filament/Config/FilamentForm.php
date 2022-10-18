<?php

namespace Kiwilan\Steward\Filament\Config;

use Closure;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kiwilan\Steward\Enums\MediaTypeEnum;
use Kiwilan\Steward\Enums\UserRoleEnum;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutCard;

class FilamentForm
{
    public static function getName(
        string $field = 'name',
        string $label = 'Name',
        bool $slug = true,
        bool $meta_title = true,
        string $helper = null,
        string $context_custom = 'edit',
        int $width = 1,
    ) {
        if (null === $helper) {
            $trans_generate = __('steward::filament.form_helper.generate');
            $trans_slug = $slug ? ' '.__('steward::filament.form_helper.slug') : '';
            $trans_meta_title = $meta_title ? ' '.__('steward::filament.form_helper.meta_title') : '';
            $trans_meta_title .= $meta_title && $slug ? ' and ' : '';
            $trans_only_create = 'edit' === $context_custom ? ', '.__('steward::filament.form_helper.only_create') : '';
            $helper = "{$trans_generate}{$trans_slug}{$trans_meta_title}{$trans_only_create}.";
        }

        return Forms\Components\TextInput::make($field)
            ->label($label)
            ->helperText($helper)
            ->required()
            ->reactive()
            ->afterStateUpdated(function (string $context, Closure $set, $state) use ($slug, $meta_title, $context_custom) {
                if ($context_custom && $context === $context_custom) {
                    return;
                }
                if ('edit' === $context) {
                    return;
                }

                if ($slug) {
                    $set('slug', Str::slug($state));
                }
                if ($meta_title) {
                    $set('meta_title', $state);
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
            /** @var Model $user */
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
