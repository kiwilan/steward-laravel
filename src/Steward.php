<?php

namespace Kiwilan\Steward;

class Steward
{
    public function embed(): string
    {
        $appEnv = config('app.env');
        $appUrl = config('app.url');
        $appUrlStorage = "$appUrl/storage";

        return <<<HTML
        <script>
            if (typeof window !== 'undefined') {
                window.appEnv = '$appEnv';
                window.appUrl = '$appUrl';
                window.appUrlStorage = '$appUrlStorage';
            }
        </script>
        HTML;
    }
}
