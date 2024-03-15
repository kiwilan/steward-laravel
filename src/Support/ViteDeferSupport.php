<?php

namespace Kiwilan\Steward\Support;

use Illuminate\Foundation\Vite;

class ViteDeferSupport extends Vite
{
    // public function script(): string
    // {
    //     $defer = config('steward.vite.defer', false);

    //     return <<<HTML
    //     <script type="module" src="{$this->manifest->entrypoints['app.js']['file']}" {$defer ? 'defer' : ''}></script>
    //     HTML;
    // }

    // public function makeScriptTag(string $url): string
    // {
    //     return sprintf('<script type="module" src="%s" defer></script>', $url);
    // }

    public function embed(): string
    {
        $gdprService = config('steward.gdpr.service');
        $gdprCookieName = config('steward.gdpr.cookie_name');
        $gdprCookieLifetime = config('steward.gdpr.cookie_lifetime');
        $gdrpMatomoEnabled = config('steward.gdpr.matomo.enabled');
        $gdprMatomoUrl = config('steward.gdpr.matomo.url');
        $gdprMatomoSiteId = config('steward.gdpr.matomo.site_id');

        return <<<HTML
        <script>
            if (typeof window !== 'undefined') {
                window.gdprService = '$gdprService';
                window.gdprCookieName = '$gdprCookieName';
                window.gdprCookieLifetime = '$gdprCookieLifetime';
                window.gdrpMatomoEnabled = '$gdrpMatomoEnabled';
                window.gdprMatomoUrl = '$gdprMatomoUrl';
                window.gdprMatomoSiteId = '$gdprMatomoSiteId';
            }
        </script>
        HTML;
    }
}
