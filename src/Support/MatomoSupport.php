<?php

namespace Kiwilan\Steward\Support;

class MatomoSupport
{
    public function embed(string $domain, string $id, string $cookieName = 'cc_cookie', bool $https = true): string
    {
        $prefix = $https ? 'https' : 'http';
        $domain = "{$prefix}://{$domain}/";

        return <<<HTML
        <!-- Matomo -->
        <script>
            function matomo() {
                function getCookie(name) {
                    const nameEQ = name + '='
                    const ca = document.cookie.split(';')
                    for (let i = 0; i < ca.length; i++) {
                        let c = ca[i]
                        while (c.charAt(0) === ' ') c = c.substring(1, c.length)
                        if (c.indexOf(nameEQ) === 0) {
                            const content = c.substring(nameEQ.length, c.length)
                            return JSON.parse(decodeURIComponent(content))
                        }
                    }

                    return undefined
                }

                function deleteCookies(name) {
                    function deleteCookie(name) {
                        document.cookie = name + "=; expires=" + (new Date(0)).toUTCString() + ";";
                    }
                    function findCookies(name) {
                        var r=[];
                        document.cookie.replace(new RegExp("("+name + "[^= ]*) *(?=\=)", "g"), function(a, b, ix){if(/[ ;]/.test(document.cookie.substr(ix-1, 1))) r.push(a.trim());})
                        return r;
                    }

                    findCookies(name).forEach(function (fullName) {
                        deleteCookie(fullName)
                    })
                }

                let consent = getCookie('$cookieName')

                if (consent && consent.categories && consent.categories.includes('analytics')) {
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
                } else if(typeof ConsentCookie !== 'undefined') {
                    deleteCookies('_pk')
                }
            }

            matomo()
        </script>
        <!-- End Matomo Code -->
        HTML;
    }
}
