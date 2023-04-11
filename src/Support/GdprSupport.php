<?php

namespace Kiwilan\Steward\Support;

class GdprSupport
{
    public function embed(): string
    {
        $appEnv = config('app.env');
        $gdprService = config('steward.gdpr.service');
        $gdprCookieName = config('steward.gdpr.cookie_name');
        $gdprCookieLifetime = config('steward.gdpr.cookie_lifetime');
        $gdrpMatomoEnabled = config('steward.gdpr.matomo.enabled');
        $gdprMatomoUrl = config('steward.gdpr.matomo.url');
        $gdprMatomoSiteId = config('steward.gdpr.matomo.site_id');

        return <<<'HTML'
        <!-- Matomo -->
        <script>
            var appEnv = <?php echo json_encode($appEnv); ?>;
            var gdprService = <?php echo json_encode($gdprService); ?>;
            var gdprCookieName = <?php echo json_encode($gdprCookieName); ?>;
            var gdprCookieLifetime = <?php echo json_encode($gdprCookieLifetime); ?>;
            var gdrpMatomoEnabled = <?php echo json_encode($gdrpMatomoEnabled); ?>;
            var gdprMatomoUrl = <?php echo json_encode($gdprMatomoUrl); ?>;
            var gdprMatomoSiteId = <?php echo json_encode($gdprMatomoSiteId); ?>;
        </script>
        <!-- End Matomo Code -->
        HTML;
    }
}
