<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Filament\Notifications\Notification;

trait Notifiable
{
    public function notify(?string $message = null, bool $success = true, ?string $title = null)
    {
        if (! $title) {
            $title = $success ? 'Saved successfully' : 'An error occurred';
        }
        $icon = $success ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle';
        $iconColor = $success ? 'success' : 'danger';

        if (! $message) {
            $message = $success ? 'Your changes have been saved.' : 'Please try again.';
        }

        if (method_exists($this, 'mount')) {
            $this->mount();
        }

        Notification::make()
            ->title($title)
            ->icon($icon)
            ->iconColor($iconColor)
            ->body($message)
            ->send()
        ;
    }
}
