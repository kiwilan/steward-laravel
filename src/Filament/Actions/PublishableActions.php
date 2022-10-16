<?php

namespace Kiwilan\Steward\Filament\Actions;

use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Kiwilan\Steward\Jobs\ProcessPublish;

class PublishableActions
{
    /**
     * Publish the given model.
     *
     * @param  string  $label The label for the model, like `posts`
     * @param  string  $model The model to publish, like `Post::class`
     *
     * @return array<Actions\Action>
     */
    public static function make(string $label, string $model)
    {
        return [
            Actions\Action::make('publish')
                ->icon('heroicon-o-paper-airplane')
                ->label('Publish all')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading("Publish all {$label}")
                ->modalSubheading("Are you sure you want to publish all {$label}?")
                ->modalButton('Publish')
                ->action(function () use ($label, $model) {
                    ProcessPublish::dispatch(model: $model, recipients: [auth()->user()]);
                    Notification::make()
                        ->title('Publishing...')
                        ->body("All {$label} will be published in background, you can close this window.")
                        ->icon('heroicon-o-paper-airplane')
                        ->iconColor('success')
                        ->send()
                    ;
                }),
            Actions\Action::make('unpublish')
                ->icon('heroicon-o-archive')
                ->label('Unpublish all')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading("Unpublish all {$label}")
                ->modalSubheading("Are you sure you want to unpublish all {$label}?")
                ->modalButton('Unpublish')
                ->action(function () use ($label, $model) {
                    ProcessPublish::dispatch(model: $model, unpublish: true, recipients: [auth()->user()]);
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
