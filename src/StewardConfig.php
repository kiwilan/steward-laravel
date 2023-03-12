<?php

namespace Kiwilan\Steward;

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

    public static function queryDefaultSortDirection(): string
    {
        return config('steward.query.default_order') ?? 'desc';
    }

    public static function queryLimit(): int
    {
        return config('steward.query.limit') ?? 25;
    }

    public static function queryFull(): bool
    {
        return config('steward.query.full') ?? false;
    }

    public static function httpPoolLimit(): int
    {
        return config('steward.http.pool_limit') ?? 200;
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

    public static function factorySeeds(): string
    {
        return config('steward.factory.seeds') ?? 'https://seeds.git-projects.xyz';
    }
}
