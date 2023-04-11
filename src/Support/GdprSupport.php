<?php

namespace Kiwilan\Steward\Support;

class GdprSupport
{
    public function embed(): string
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
            let appEnv = '$appEnv';
            let appUrl = '$appUrl';
            let appUrlStorage = '$appUrlStorage';
            let gdprService = '$gdprService';
            let gdprCookieName = '$gdprCookieName';
            let gdprCookieLifetime = '$gdprCookieLifetime';
            let gdrpMatomoEnabled = '$gdrpMatomoEnabled';
            let gdprMatomoUrl = '$gdprMatomoUrl';
            let gdprMatomoSiteId = '$gdprMatomoSiteId';
        </script>
        <!-- End Matomo Code -->
        HTML;
    }
}
