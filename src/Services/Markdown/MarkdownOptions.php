<?php

namespace Kiwilan\Steward\Services\Markdown;

class MarkdownOptions
{
    public function __construct(
        protected array $dotenv = [
            'APP_NAME' => 'app.name',
            'APP_URL' => 'app.url',
        ],
        protected ?string $imagePath = null,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function dotenv(): array
    {
        return $this->dotenv;
    }

    public function imagesPath(): ?string
    {
        return $this->imagePath;
    }
}
