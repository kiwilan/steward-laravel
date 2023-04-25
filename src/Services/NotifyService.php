<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Services\Notify\DiscordNotify;

class NotifyService
{
    protected function __construct(
        protected ?string $message = null,
        protected NotifyApplication $application = NotifyApplication::discord,
        protected array $options = [],
        protected bool $success = false,
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    public function to(NotifyApplication $application = NotifyApplication::discord, array $options = []): self
    {
        $this->application = $application;
        $this->options = $options;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function send()
    {
        $notified = match ($this->application) {
            NotifyApplication::discord => $this->success = DiscordNotify::make($this->options, $this->message),
            // default => throw new \Exception("Unknown app {$this->application}"),
        };

        $this->success = $notified;

        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}

enum NotifyApplication: string
{
    case discord = 'discord';
}
