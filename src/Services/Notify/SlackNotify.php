<?php

namespace Kiwilan\Steward\Services\Notify;

use Kiwilan\Steward\Services\NotifyApplication;

class SlackNotify extends Notifying
{
    public static function send(array $options, string $message): self
    {
        $self = self::prepare(new self($options, $message), NotifyApplication::slack);

        // $options = [];
        // $options['id'] = $self->options[0] ?? null;
        // $options['token'] = $self->options[1] ?? null;
        // $options['channel'] = $self->options[2] ?? null;

        // $self->options = $options;

        if (! $self->options['id'] || ! $self->options['token'] || ! $self->options['channel']) {
            throw new \Exception("Missing ID or token for server {$self->options['id']}:{$self->options['token']}");
        }

        return $self;
    }

    protected function setDefaultOptions(): self
    {
        if (empty($this->options)) {
            $options = $this->defaultOptions;
            $this->options = explode(':', $options);
        }

        $options = [];
        $options['id'] = $this->options[0] ?? null;
        $options['token'] = $this->options[1] ?? null;
        $options['channel'] = $this->options[2] ?? null;

        $this->options = $options;

        return $this;
    }
}
