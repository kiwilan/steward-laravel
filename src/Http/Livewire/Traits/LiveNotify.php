<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Filament\Notifications\Notification;

/**
 * `Livewire\Component` trait to send notifications with `Filament\Notifications`.
 */
trait LiveNotify
{
    /**
     * Send notification to user.
     */
    public function notify(string $message = null): LiveNotifyItem
    {
        if (method_exists($this, 'mount')) {
            $this->mount();
        }

        return new LiveNotifyItem(message: $message);
    }
}

class LiveNotifyItem
{
    public function __construct(
        protected string $title = 'Information',
        protected string $message = 'Notification message',
        protected string $icon = 'heroicon-o-information-circle',
        protected string $iconColor = 'primary',
    ) {
    }

    /**
     * Set notification title.
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Preset notification as success.
     */
    public function success(): self
    {
        $this->title = 'Saved successfully';
        $this->message = 'Your changes have been saved.';
        $this->icon = 'heroicon-o-check-circle';
        $this->iconColor = 'success';

        return $this;
    }

    /**
     * Preset notification as error.
     */
    public function error(): self
    {
        $this->title = 'An error occurred';
        $this->message = 'Please try again.';
        $this->icon = 'heroicon-o-exclamation-circle';
        $this->iconColor = 'danger';

        return $this;
    }

    /**
     * Send notification.
     */
    public function send(): void
    {
        Notification::make()
            ->title($this->title)
            ->icon($this->icon)
            ->iconColor($this->iconColor)
            ->body($this->message)
            ->send()
        ;
    }
}
