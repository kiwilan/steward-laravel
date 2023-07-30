<?php

namespace Kiwilan\Steward\Services;

class GravatarService
{
    protected function __construct(
        protected string $default = 'retro', // 404, mp, identicon, monsterid, wavatar, retro, robohash, blank
        protected int $size = 200,
        protected string $rating = 'g', // g, pg, r, x
        protected ?string $email = null,
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * Default Gravatar image
     *
     * - 404: do not load any image if none is associated with the email hash, instead return an HTTP 404 (File Not Found) response
     * - mp: (mystery-person) a simple, cartoon-style silhouetted outline of a person (does not vary by email hash)
     * - identicon: a geometric pattern based on an email hash
     * - monsterid: a generated 'monster' with different colors, faces, etc
     * - wavatar: generated faces with differing features and backgrounds
     * - retro: awesome generated, 8-bit arcade-style pixelated faces
     * - robohash: a generated robot with different colors, faces, etc
     * - blank: a transparent PNG image (border added to HTML below for demonstration purposes)
     */
    public function default(string $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function email(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Size in pixels, defaults to 80px [ 1 - 2048 ]
     */
    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Maximum rating (inclusive) [ g | pg | r | x ]
     */
    public function rating(string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function get(): string
    {
        if (! $this->email) {
            $this->email = $this->randomString(10).'@'.$this->randomString(10).'.com';
        }

        $hash = md5(strtolower(trim($this->email)));
        $defaultGravatarSize = $this->size;
        $defaultGravatarRating = $this->rating;
        $defaultGravatarDefault = $this->default;

        return "https://www.gravatar.com/avatar/{$hash}?s={$defaultGravatarSize}&r={$defaultGravatarRating}&d={$defaultGravatarDefault}";
    }

    private function randomString(int $length = 32): string
    {
        $bytes = random_bytes($length);

        return bin2hex($bytes);
    }
}
