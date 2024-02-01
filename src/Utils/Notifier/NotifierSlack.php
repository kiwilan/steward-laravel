<?php

namespace Kiwilan\Steward\Utils\Notifier;

use Kiwilan\Steward\Utils\Notifier;

class NotifierSlack extends Notifier implements INotifier
{
    protected function __construct(
        protected string $webhook,
        protected ?string $message = null,
    ) {
    }

    public static function make(string $webhook): self
    {
        return new self($webhook);
    }

    /**
     * @param  string|string[]  $message
     */
    public function message(array|string $message): self
    {
        if (is_array($message)) {
            $message = implode(PHP_EOL, $message);
        }

        $this->message = $message;

        return $this;
    }

    public function send(): bool
    {
        $res = $this->sendRequest($this->webhook, bodyJson: [
            'text' => $this->message,
        ]);

        if ($res->getStatusCode() !== 200) {
            $this->logError("status code {$res->getStatusCode()}, {$res->getBody()->getContents()}");

            return false;
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'webhook' => $this->webhook,
            'message' => $this->message,
        ];
    }
}
