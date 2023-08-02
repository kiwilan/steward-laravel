<?php

namespace Kiwilan\Steward\Filament\Actions;

use Filament\Notifications\Notification;
use Kiwilan\Steward\Jobs\ProcessPublish;

class PublishableActions
{
    /**
     * Publish the given model.
     *
     * @param  string  $label The label for the model, like `posts`
     * @param  string  $model The model to publish, like `Post::class`
     * @return array<\Filament\Actions\Action>
     */
    public static function make(string $label, string $model, bool $withIcon = false)
    {
        return [
            \Filament\Actions\Action::make('publish')
                ->icon($withIcon ? 'heroicon-o-paper-airplane' : null)
                ->label('Publish all')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading("Publish all {$label}")
                // ->modalSubheading("Are you sure you want to publish all draft and scheduled {$label}? The published date will set to now.")
                // ->modalButton('Publish')
                ->action(function () use ($label, $model) {
                    ProcessPublish::dispatch(label: $label, model: $model, recipients: [auth()->user()]);
                    Notification::make()
                        ->title('Publishing...')
                        ->body("All {$label} will be published in background, you can close this window.")
                        ->icon('heroicon-o-paper-airplane')
                        ->iconColor('success')
                        ->send()
                    ;
                }),
            \Filament\Actions\Action::make('unpublish')
                ->icon($withIcon ? 'heroicon-o-archive' : null)
                ->label('Unpublish all')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading("Unpublish all {$label}")
                // ->modalSubheading("Are you sure you want to unpublish all {$label}?")
                // ->modalButton('Unpublish')
                ->action(function () use ($label, $model) {
                    ProcessPublish::dispatch(label: $label, model: $model, unpublish: true, recipients: [auth()->user()]);
                    Notification::make()
                        ->title('Unpublishing...')
                        ->body("All {$label} will be unpublished in background, you can close this window.")
                        ->icon('heroicon-o-archive')
                        ->iconColor('success')
                        ->send()
                    ;
                }),
        ];
    }
}
