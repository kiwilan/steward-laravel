<?php

namespace Kiwilan\Steward\Services\Notify;

use Kiwilan\Steward\StewardConfig;
use Psr\Http\Message\ResponseInterface;

abstract class Notifying
{
    protected function __construct(
        protected ?string $url = null,
        protected ?string $message = null,
        protected array $options = [],
        protected bool $success = false,
        protected ?ResponseInterface $response = null,
    ) {
    }

    abstract public static function send(array $options, string $message): self;

    public function url(): ?string
    {
        return $this->url;
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function options(): array
    {
        return $this->options;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function response(): ?ResponseInterface
    {
        return $this->response;
    }

    // protected function parseLocalConfig(): array
    // {
    //     $servers = [];
    //     $config = StewardConfig::notifyDiscordServers();

    //     foreach ($config as $key => $value) {
    //         $value = explode(':', $value);
    //         $id = $value[0] ?? null;
    //         $token = $value[1] ?? null;

    //         $servers[$key] = [
    //             'name' => $key,
    //             'id' => $id,
    //             'token' => $token,
    //         ];

    //         if (! $id || ! $token) {
    //             throw new \Exception("Missing ID or token for server {$key}={$id}:{$token}");
    //         }
    //     }

    //     return $servers;
    // }

    // protected function parseInlineConfig(?string $inlineConfig = null): array
    // {
    //     if (! $inlineConfig) {
    //         return [];
    //     }

    //     $servers = [];
    //     $config = explode(',', $inlineConfig);

    //     foreach ($config as $key => $value) {
    //         $value = explode(':', $value);
    //         $name = $value[0] ?? null;
    //         $id = $value[1] ?? null;
    //         $token = $value[2] ?? null;

    //         $servers[$name] = [
    //             'name' => $name,
    //             'id' => $id,
    //             'token' => $token,
    //         ];
    //     }

    //     return $servers;
    // }

    protected function handleSendTo(?string $sendto = null): array
    {
        if (! $sendto) {
            return [];
        }

        $data = explode(':', $sendto);
        $app = $data[0] ?? null;
        $id = $data[1] ?? null;
        $token = $data[2] ?? null;

        if (! $app || ! $id || ! $token) {
            throw new \Exception('Invalid sendto pattern.');
        }

        return [
            'app' => $app,
            'id' => $id,
            'token' => $token,
        ];
    }
}
