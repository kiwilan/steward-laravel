<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;
use Kiwilan\Steward\Utils\Notifier\NotifierMail;
use Symfony\Component\Mime\Email;

/**
 * Send notifications to email, Slack or Discord.
 */
class Notifier
{
    protected function __construct(
        protected ?string $type = null,
        protected ?string $message = null,
        protected ?NotifierMail $mail = null,
        protected ?string $slack = null,
        protected ?string $discord = null,
        protected bool $success = false,
    ) {
    }

    /**
     * Send notification to email.
     */
    public static function mail(): NotifierMail
    {
        $self = new self();

        $self->type = 'mail';
        $self->mail = NotifierMail::make($self);

        return $self->mail;
    }

    /**
     * Send notification to Slack channel via webhook.
     *
     * @param  string  $webhook  Slack webhook URL, like `https://hooks.slack.com/services/X/Y/Z`
     *
     * @see https://api.slack.com/messaging/webhooks
     */
    public static function slack(string $webhook): self
    {
        $self = new self();

        $self->type = 'slack';
        $self->slack = $webhook;

        return $self;
    }

    /**
     * Send notification to Discord channel via webhook.
     *
     * @param  string  $webhook  Discord webhook URL, like `https://discord.com/api/webhooks/X/Y`
     *
     * @see https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks
     */
    public static function discord(string $webhook): self
    {
        $self = new self();

        $self->type = 'discord';
        $self->discord = $webhook;

        return $self;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function send(): bool
    {
        if (! $this->type) {
            throw new \Exception('Notifier type is not defined');
        }

        if (! $this->message) {
            throw new \Exception('Notifier message is not defined');
        }

        Log::debug("Sending {$this->type} notification: {$this->message}...");

        try {
            if ($this->type === 'slack') {
                $this->sendToSlack();
            }

            if ($this->type === 'discord') {
                $this->sendToDiscord();
            }
        } catch (\Throwable $th) {
            Log::error("{$this->type} notification failed: {$th->getMessage()}");

            return false;
        }

        Log::debug("{$this->type} notification sent");

        return $this->success;
    }

    private function sendToDiscord(): void
    {
        $res = $this->sendRequest($this->discord, 'content');
        if ($res->getStatusCode() !== 204) {
            Log::error('Discord notification failed', [
                'response' => $res->getBody()->getContents(),
            ]);
        } else {
            $this->success = true;
        }
    }

    private function sendToSlack(): void
    {
        $res = $this->sendRequest($this->slack, 'text');
        if ($res->getStatusCode() !== 200) {
            Log::error('Slack notification failed', [
                'response' => $res->getBody()->getContents(),
            ]);
        } else {
            $this->success = true;
        }
    }

    private function sendRequest(string $url, string $bodyName): \Psr\Http\Message\ResponseInterface
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                $bodyName => $this->message,
            ],
            'http_errors' => false,
        ]);

        return $response;
    }
}
