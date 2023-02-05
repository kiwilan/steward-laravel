@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    @isset($title)
        {{ $title }}
    @else
        @if ($inertia)
            <title inertia>
            @else
                <title>
        @endif
        {{ config('app.name', 'Laravel') }}
        </title>
    @endisset

    @if ($darkMode)
        {{-- <script>
            const colorScheme = localStorage.getItem('color-scheme')

            if (colorScheme) {
                document.documentElement.classList.toggle(colorScheme, true)
            } else {
                const system = window.matchMedia &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches ?
                    'dark' :
                    'light'
                document.documentElement.classList.toggle(system, true)
            }
        </script> --}}
        @darkMode
    @endif

    <!-- Scripts -->
    @if ($ziggy)
        @routes
    @endif
    @if ($vite)
        @vite
    @endif
    @if ($inertia)
        @inertiaHead
    @endif
</head>

<body class="font-sans antialiased {{ config('app.env') === 'local' ? 'debug-screens' : '' }}">
    @if ($inertia)
        @inertia
    @else
        {{ $slot }}
    @endif
</body>

</html>
