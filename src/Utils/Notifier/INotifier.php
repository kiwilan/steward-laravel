<?php

namespace Kiwilan\Steward\Utils\Notifier;

interface INotifier
{
    /**
     * @param  string|string[]  $message
     */
    public function message(array|string $message): self;

    public function send(): bool;

    public function toArray(): array;
}
