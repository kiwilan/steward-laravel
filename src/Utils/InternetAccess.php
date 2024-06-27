<?php

namespace Kiwilan\Steward\Utils;

class InternetAccess
{
    protected function __construct(
        protected string $provider = 'www.google.com',
        protected bool $force_https = false,
        protected ?string $url = null,
        protected ?string $status = null,
        protected ?int $status_code = null,
        protected bool $is_available = false
    ) {
    }

    /**
     * Check if the internet is available.
     *
     * @param  string  $provider  The provider to check, default is Google. You can use 'www.google.com' or 'whatismyipaddress.com'
     * @param  bool  $force_https  Force the connection to use HTTPS
     */
    public static function check(string $provider = 'www.google.com', bool $force_https = false): self
    {
        $self = new self($provider, $force_https);

        if (str_contains($self->provider, 'http://')) {
            $self->provider = str_replace('http://', '', $self->provider);
        }

        if (str_contains($self->provider, 'https://')) {
            $self->provider = str_replace('https://', '', $self->provider);
        }

        if ($force_https) {
            $self->url = "https://{$self->provider}";
        } else {
            $self->url = "http://{$self->provider}";
        }

        try {
            [$status] = get_headers($self->url);
            $self->status = $status;
            $self->status_code = (int) explode(' ', $status)[1];
        } catch (\Throwable $th) {
            return $self;
        }

        $self->is_available = true;

        return $self;
    }

    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    public function isOk(): bool
    {
        return $this->status_code === 200;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getStatusCode(): ?int
    {
        return $this->status_code;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getForceHttps(): bool
    {
        return $this->force_https;
    }
}
