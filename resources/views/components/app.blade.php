@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if ($darkMode)
    <link rel="shortcut icon" href="{{ asset('/favicon.svg') }}" type="image/x-icon"
      media="(prefers-color-scheme: light)">
    <link rel="shortcut icon" href="{{ asset('/favicon-dark.svg') }}" type="image/x-icon"
      media="(prefers-color-scheme: dark)">
  @else
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
  @endif
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/apple-touch-icon.png') }}">
  <link rel="manifest" href="/site.webmanifest">

  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/favicon-16x16.png') }}">
  <meta name="msapplication-TileColor" content="{{ $tile }}">
  <meta name="theme-color" content="{{ $theme }}">

  @stack('head')

  @if ($title)
    {{ $title }}
  @elseif ($seo)
    {!! SEO::generate() !!}
  @else
    @if ($inertiaEnabled)
      <title inertia>{{ config('app.name', 'Laravel') }}</title>
    @else
      <title>{{ config('app.name', 'Laravel') }}</title>
    @endif
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
  @if ($livewire)
    @livewireStyles
  @endif
</head>

<body class="font-sans antialiased {{ config('app.env') === 'local' ? 'debug-screens' : '' }}">
@if ($inertiaEnabled)
  @inertia
@endif
{{ $slot }}
@stack('modals')

@stack('scripts')
@if ($livewire)
  @livewire('notifications')
  @livewireScripts
@endif
</body>

</html>
