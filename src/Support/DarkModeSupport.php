<?php

namespace Kiwilan\Steward\Support;

class DarkModeSupport
{
    public function embed(string $item = 'color-scheme'): string
    {
        return <<<HTML
        <script>
            const colorScheme = localStorage.getItem('$item')

            if (colorScheme) {
                document.documentElement.classList.toggle(colorScheme, true)
            } else {
                const system = window.matchMedia &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches ?
                    'dark' :
                    'light'
                document.documentElement.classList.toggle(system, true)
                localStorage.setItem('$item', system)
            }
        </script>
        HTML;
    }
}
