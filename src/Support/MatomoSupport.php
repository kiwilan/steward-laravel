<?php

namespace Kiwilan\Steward\Support;

class MatomoSupport
{
    public function embed(string $domain, string $id, bool $https = true): string
    {
        $prefix = $https ? 'https' : 'http';
        $domain = "{$prefix}://{$domain}/";

        return <<<HTML
        <!-- Matomo -->
        <script>
            var _paq = window._paq = window._paq || [];
            /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
            _paq.push(['trackPageView']);
            _paq.push(['enableLinkTracking']);
            (function() {
            var u = '$domain';
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '$id']);
            var d = document,
                g = d.createElement('script'),
                s = d.getElementsByTagName('script')[0];
            g.async = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
            })();
        </script>
        <!-- End Matomo Code -->
        HTML;
    }
}
