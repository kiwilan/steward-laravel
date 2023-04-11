<?php

namespace Kiwilan\Steward\Support;

class GdprSupport
{
    public function embed(bool $enabled = true): string
    {
        $appEnv = config('app.env');
        $appUrl = config('app.url');
        $appUrlStorage = "$appUrl/storage";

        $gdprService = config('steward.gdpr.service');
        $gdprCookieName = config('steward.gdpr.cookie_name');
        $gdprCookieLifetime = config('steward.gdpr.cookie_lifetime');
        $gdrpMatomoEnabled = config('steward.gdpr.matomo.enabled');
        $gdprMatomoUrl = config('steward.gdpr.matomo.url');
        $gdprMatomoSiteId = config('steward.gdpr.matomo.site_id');

        return <<<HTML
        <!-- Matomo -->
        <script>
            window.appEnv = '$appEnv';
            window.appUrl = '$appUrl';
            window.appUrlStorage = '$appUrlStorage';
            window.gdprService = '$gdprService';
            window.gdprCookieName = '$gdprCookieName';
            window.gdprCookieLifetime = '$gdprCookieLifetime';
            window.gdrpMatomoEnabled = '$gdrpMatomoEnabled';
            window.gdprMatomoUrl = '$gdprMatomoUrl';
            window.gdprMatomoSiteId = '$gdprMatomoSiteId';
        </script>
        <!-- End Matomo Code -->
        HTML;
    }
}
