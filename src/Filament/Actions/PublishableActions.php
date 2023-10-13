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
                ->label(__('steward::filament.actions.publish_button'))
                ->outlined()
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading(__('steward::filament.actions.publish_button')." {$label}")
                ->modalDescription(__('steward::filament.actions.publish_text'))
                ->action(function () use ($label, $model) {
                    ProcessPublish::dispatch(label: $label, model: $model, recipients: [auth()->user()]);
                    Notification::make()
                        ->title(__('steward::filament.actions.publish_notification_title'))
                        ->body(__('steward::filament.actions.publish_notification_body', ['label' => $label]))
                        ->icon('heroicon-o-paper-airplane')
                        ->iconColor('success')
                        ->send()
                    ;
                }),
            \Filament\Actions\Action::make('unpublish')
                ->icon($withIcon ? 'heroicon-o-archive' : null)
                ->label(__('steward::filament.actions.unpublish_button'))
                ->outlined()
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('steward::filament.actions.unpublish_button')." {$label}")
                ->modalDescription(__('steward::filament.actions.unpublish_text'))
                ->action(function () use ($label, $model) {
                    ProcessPublish::dispatch(label: $label, model: $model, unpublish: true, recipients: [auth()->user()]);
                    Notification::make()
                        ->title(__('steward::filament.actions.unpublish_notification_title'))
                        ->body(__('steward::filament.actions.unpublish_notification_body', ['label' => $label]))
                        ->icon('heroicon-o-archive')
                        ->iconColor('success')
                        ->send()
                    ;
                }),
        ];
    }
}
