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

    @stack('head')

    @isset($title)
        {{ $title }}
    @else
        @if ($inertiaEnabled)
            <title inertia>
            @else
                <title>
        @endif
        {{ config('app.name', 'Laravel') }}
        </title>
    @endisset

    @if ($darkMode)
        @darkMode
    @endif

    <!-- Scripts -->
    @if ($ziggy)
        @routes
    @endif
    @if ($vite)
        @vite($vite)
    @endif
    @if ($inertiaEnabled)
        @inertiaHead
    @endif
</head>

<body class="font-sans antialiased {{ config('app.env') === 'local' ? 'debug-screens' : '' }}">
    @if ($inertiaEnabled)
        @inertia
    @else
        {{ $slot }}
    @endif
</body>

</html>
