<?php

namespace Kiwilan\Steward\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $site_name = '';

    public ?string $site_description = '';

    public ?string $site_url = '';

    public ?string $site_lang = '';

    public bool $site_active = false;

    public ?string $site_utc = '';

    public ?string $site_favicon = '';

    public ?string $site_color = '';

    public ?string $default_image = '';

    /** @var string[] */
    public array $social = [];

    /** @var string[] */
    public array $social_share = [];

    public static function group(): string
    {
        return 'general';
    }
}
