<?php

namespace Kiwilan\Steward\Utils;

use Illuminate\Support\Facades\Log;

/**
 * @deprecated Use `Kiwilan\Steward\Utils\Notifier` instead.
 */
class Discord
{
    protected function __construct(
        protected string $url,
        protected ?string $username = null,
        protected ?string $message = null,
    ) {
    }

    public static function make(string $url): self
    {
        return new self($url);
    }

    public function username(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function message(array|string $message): static
    {
        if (is_array($message)) {
            $message = implode(PHP_EOL, $message);
        }

        $this->message = $message;

        return $this;
    }

    public function send(): bool
    {
        $data = [
            'content' => $this->message,
        ];

        if ($this->username) {
            $data['username'] = $this->username;
        }

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        file_get_contents($this->url, false, $context);

        if ($http_response_header[0] !== 'HTTP/1.1 204 No Content') {
            Log::error("Discord webhook not send {$this->url}", [
                'http_response_header' => $http_response_header,
            ]);

            return false;
        }

        return true;
    }
}
