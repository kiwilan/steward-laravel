<?php

namespace Kiwilan\Steward;

use Kiwilan\Steward\Enums\Api\SeedsApiCategoryEnum;
use Kiwilan\Steward\Enums\Api\SeedsApiSizeEnum;
use Kiwilan\Steward\Enums\FactoryTextEnum;

class StewardConfig
{
    public static function authLoginRoute(): string
    {
        return config('steward.auth.login_route') ?? 'login';
    }

    public static function authRegisterRoute(): string
    {
        return config('steward.auth.register_route') ?? 'register';
    }

    public static function authLogoutRoute(): string
    {
        return config('steward.auth.logout_route') ?? 'logout';
    }

    public static function authHomeRoute(): string
    {
        return config('steward.auth.home_route') ?? 'home';
    }

    public static function mediableDefault(): string
    {
        return config('steward.mediable.default') ?? 'https://raw.githubusercontent.com/kiwilan/steward-laravel/main/public/no-image-available.jpg';
    }

    public static function mediableExtensions(): array
    {
        return config('steward.mediable.extensions') ?? ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'];
    }

    public static function mediableFormat(): string
    {
        return config('steward.mediable.format') ?? env('STEWARD_MEDIABLE_FORMAT', 'webp');
    }

    public static function templateEnum(): string
    {
        return config('steward.template.enum') ?? \Kiwilan\Steward\Enums\TemplateEnum::class;
    }

    public static function builderEnum(): string
    {
        return config('steward.builder.enum') ?? \Kiwilan\Steward\Enums\BuilderEnum::class;
    }

    public static function submissionModel(): string
    {
        return config('steward.submission.model') ?? \Kiwilan\Steward\Models\Submission::class;
    }

    public static function scribeEndpoints(): array
    {
        return config('steward.scribe.endpoints') ?? [];
    }

    public static function filamentLogoDefault(): string
    {
        return config('steward.filament.logo.default') ?? 'images/logo.svg';
    }

    public static function filamentLogoDark(): string
    {
        return config('steward.filament.logo.dark') ?? 'images/logo-dark.svg';
    }

    public static function filamentLogoInlineDefault(): string
    {
        return config('steward.filament.logo-inline.default') ?? 'images/logo-inline.svg';
    }

    public static function filamentLogoInlineDark(): string
    {
        return config('steward.filament.logo-inline.dark') ?? 'images/logo-inline-dark.svg';
    }

    public static function filamentWidgetsWelcomeUrl(): string
    {
        return config('steward.filament.widgets.welcome.url') ?? 'https://filamentphp.com/docs';
    }

    public static function filamentWidgetsWelcomeLabel(): string
    {
        return config('steward.filament.widgets.welcome.label') ?? 'filament::widgets/filament-info-widget.buttons.visit_documentation.label';
    }

    public static function queryDefaultSort(): string
    {
        return config('steward.query.default_sort') ?? 'id';
    }

    public static function queryPagination(): int
    {
        return config('steward.query.pagination') ?? 25;
    }

    public static function queryNoPaginate(): bool
    {
        return config('steward.query.no_paginate') ?? false;
    }

    public static function httpPoolLimit(): int
    {
        return config('steward.http.pool_limit') ?? 250;
    }

    public static function httpAsyncAllow(): bool
    {
        return config('steward.http.async_allow') ?? true;
    }

    public static function iframelyApi(): string
    {
        return config('steward.iframely.api') ?? 'https://iframe.ly/com';
    }

    public static function iframelyKey(): string
    {
        return config('steward.iframely.key') ?? '';
    }

    public static function componentsConfig(): array
    {
        return config('steward.components.config') ?? [];
    }

    public static function factoryText(): FactoryTextEnum
    {
        return config('steward.factory.text') ?? FactoryTextEnum::lorem;
    }

    public static function factoryMediaDownloaderDefaultCategory(): SeedsApiCategoryEnum
    {
        return config('steward.factory.media_downloader.default_category') ?? SeedsApiCategoryEnum::all;
    }

    public static function factoryMediaDownloaderDefaultSize(): SeedsApiSizeEnum
    {
        return config('steward.factory.media_downloader.default_size') ?? SeedsApiSizeEnum::medium;
    }

    public static function factoryMediaDownloaderSeedsApi(): string
    {
        return config('steward.factory.media_downloader.seeds.api') ?? 'https://seeds.git-projects.xyz';
    }

    public static function factoryMaxHandle(): int
    {
        return config('steward.factory.max_handle') ?? 1000;
    }

    public static function factoryVerbose(): string
    {
        return config('steward.factory.verbose') ?? false;
    }

    public static function gdprService(): string
    {
        return config('steward.gdpr.service') ?? 'orestbida/cookieconsent';
    }

    public static function gdprCookieName(): string
    {
        return config('steward.gdpr.cookie_name') ?? 'cc_cookie';
    }

    public static function gdprCookieLifetime(): int
    {
        return config('steward.gdpr.cookie_lifetime') ?? 182;
    }

    public static function gdprMatomoEnabled(): bool
    {
        return config('steward.gdpr.matomo.enabled') ?? false;
    }

    public static function gdprMatomoUrl(): ?string
    {
        return config('steward.gdpr.matomo.url') ?? null;
    }

    public static function gdprMatomoSiteId(): ?string
    {
        return config('steward.gdpr.matomo.site_id') ?? null;
    }

    public static function notifyDefault(): ?string
    {
        return config('steward.notify.default') ?? 'discord';
    }

    public static function notifyDiscord(): ?string
    {
        return config('steward.notify.discord') ?? null;
    }

    public static function notifySlack(): ?string
    {
        return config('steward.notify.slack') ?? null;
    }

    public static function livewirePaginationTheme(): string
    {
        return config('steward.livewire.pagination.theme') ?? 'tailwind';
    }

    public static function livewirePaginationDefault(): int
    {
        return config('steward.livewire.pagination.default') ?? 20;
    }

    public static function livewirePaginationOptions(): array
    {
        return config('steward.livewire.pagination.options') ?? [10, 20, 50, 100];
    }
}
