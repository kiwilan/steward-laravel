<?php

namespace Kiwilan\Steward\Services\FactoryService\Providers;

class ImageProvider
{
    /** @var string[] */
    protected array $headers = [];

    /** @var string[] */
    protected array $urlsList = [];

    protected function __construct(
        protected int $count = 1,
        protected int $width = 600,
        protected int $height = 600,
        protected ?string $apiKey = null,
        protected string $provider = 'picsum', // picsum, api_ninja, pixabay
        protected ?string $url = null,
    ) {
    }

    public static function make(int $count = 1, int $width = 600, int $height = 600): self
    {
        $self = new self();
        $self->count = $count;
        $self->width = $width;
        $self->height = $height;
        $self->provider = config('steward.factory.provider', 'picsum');
        $self->apiKey = config('steward.factory.provider_key');

        return $self;
    }

    public function usePicsum(): self
    {
        $this->provider = 'picsum';

        return $this;
    }

    public function useApiNinja(): self
    {
        $this->provider = 'api_ninja';

        return $this;
    }

    public function usePixabay(): self
    {
        $this->provider = 'pixabay';

        return $this;
    }

    public function useApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function get(): self
    {
        match ($this->provider) {
            'picsum' => $this->picsumProvider(),
            'api_ninja' => $this->apiNinjaProvider(),
            'pixabay' => $this->pixabayProvider(),
            default => $this->picsumProvider(),
        };

        $list = [];

        for ($i = 0; $i < $this->count; $i++) {
            $list[] = $this->url;
        }

        $this->urlsList = $list;

        return $this;
    }

    /**
     * @return string[]
     */
    public function urlsList(): array
    {
        return $this->urlsList;
    }

    /**
     * @return string[]
     */
    public function headers(): array
    {
        return $this->headers;
    }

    private function picsumProvider()
    {
        $this->url = "https://picsum.photos/{$this->width}/{$this->height}";
    }

    private function apiNinjaProvider()
    {
        $endpoint = 'https://api.api-ninjas.com/v1/randomimage';

        $query = [
            'width' => $this->width,
            'height' => $this->height,
        ];

        $this->url = "{$endpoint}?".http_build_query($query);

        $this->headers = [
            'X-Api-Key' => $this->apiKey,
            'Accept' => 'image/jpg',
        ];
    }

    private function pixabayProvider()
    {
        $endpoint = 'https://pixabay.com/api/';

        $query = [
            'key' => $this->apiKey,
            'q' => 'yellow+flowers',
            'image_type' => 'photo',
            'pretty' => 'true',
        ];

        $this->url = "{$endpoint}?".http_build_query($query);
    }
}
