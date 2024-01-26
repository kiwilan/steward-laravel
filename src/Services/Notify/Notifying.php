<?php

namespace Kiwilan\Steward\Services\Notify;

use Kiwilan\Steward\Services\NotifyApplication;
use Kiwilan\Steward\StewardConfig;
use Psr\Http\Message\ResponseInterface;

/**
 * @deprecated
 */
abstract class Notifying
{
    protected function __construct(
        protected array $options = [],
        protected ?string $message = null,
        protected ?NotifyApplication $application = null,
        protected ?string $defaultOptions = null,
        protected ?string $url = null,
        protected ?ResponseInterface $response = null,
        protected bool $success = false,
    ) {
    }

    abstract public static function send(array $options, string $message): self;

    abstract protected function setDefaultOptions(): self;

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

    protected static function prepare(Notifying $instance, NotifyApplication $application)
    {
        $self = $instance;
        $self->application = $application;

        $self->defaultOptions = match ($self->application) {
            NotifyApplication::discord => StewardConfig::notifyDiscord(),
            NotifyApplication::slack => StewardConfig::notifySlack(),
        };

        $self->setDefaultOptions();

        $baseUrl = match ($self->application) {
            NotifyApplication::discord => 'https://discord.com/api/webhooks/',
            NotifyApplication::slack => 'https://hooks.slack.com/services/',
        };

        $config = match ($self->application) {
            NotifyApplication::discord => [
                'body' => 'content',
                'code' => 204,
            ],
            NotifyApplication::slack => [
                'body' => 'text',
                'code' => 200,
            ],
        };

        $params = implode('/', $self->options);
        $self->url = "{$baseUrl}{$params}";

        $self->guzzle($config);

        return $self;
    }

    protected function guzzle(array $config): self
    {
        $client = new \GuzzleHttp\Client();
        $this->response = $client->request('POST', $this->url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                $config['body'] => $this->message,
            ],
            'http_errors' => false,
        ]);

        $code = $this->response->getStatusCode();

        if ($code === $config['code']) {
            $this->success = true;
        }

        return $this;
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
