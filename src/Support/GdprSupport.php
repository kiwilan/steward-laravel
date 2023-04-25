<?php

namespace Kiwilan\Steward\Support;

class GdprSupport
{
    public function embed(): string
    {
        $gdprService = config('steward.gdpr.service');
        $gdprCookieName = config('steward.gdpr.cookie_name');
        $gdprCookieLifetime = config('steward.gdpr.cookie_lifetime');
        $gdrpMatomoEnabled = config('steward.gdpr.matomo.enabled');
        $gdprMatomoUrl = config('steward.gdpr.matomo.url');
        $gdprMatomoSiteId = config('steward.gdpr.matomo.site_id');

        return <<<HTML
        <!-- GDPR -->
        <script>
            window.gdprService = '$gdprService';
            window.gdprCookieName = '$gdprCookieName';
            window.gdprCookieLifetime = '$gdprCookieLifetime';
            window.gdrpMatomoEnabled = '$gdrpMatomoEnabled';
            window.gdprMatomoUrl = '$gdprMatomoUrl';
            window.gdprMatomoSiteId = '$gdprMatomoSiteId';
        </script>
        <!-- End GDPR -->
        HTML;
    }
}
