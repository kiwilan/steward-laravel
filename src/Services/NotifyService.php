<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Services\Notify\DiscordNotify;
use Kiwilan\Steward\Services\Notify\Notifying;
use Kiwilan\Steward\Services\Notify\SlackNotify;
use Kiwilan\Steward\StewardConfig;

class NotifyService
{
    protected function __construct(
        protected ?string $message = null,
        protected NotifyApplication $application = NotifyApplication::discord,
        protected array $options = [],
        protected bool $success = false,
        protected ?Notifying $notify = null,
    ) {
    }

    public static function make(): self
    {
        $self = new self();
        $self->application = NotifyApplication::tryFrom(StewardConfig::notifyDefault());

        return $self;
    }

    public function to(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param  NotifyApplication|string  $application default `NotifyApplication::discord`, can be string of application like `discord`
     */
    public function application(NotifyApplication|string $application = null): self
    {
        if (! $application) {
            $application = StewardConfig::notifyDefault();
        }

        if (is_string($application)) {
            $application = match ($application) {
                'discord' => NotifyApplication::discord,
                'slack' => NotifyApplication::slack,
                default => throw new \Exception("Unknown app {$application}"),
            };
        }

        $this->application = $application;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function send()
    {
        $this->notify = match ($this->application) {
            NotifyApplication::discord => DiscordNotify::send($this->options, $this->message),
            NotifyApplication::slack => SlackNotify::send($this->options, $this->message),
        };

        $this->success = $this->notify->isSuccess();

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

    case slack = 'slack';
}
