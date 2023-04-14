@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta
    http-equiv="X-UA-Compatible"
    content="IE=edge"
  >
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1"
  >
  <meta
    name="csrf-token"
    content="{{ csrf_token() }}"
  >

  @if ($darkMode)
    <link
      type="image/x-icon"
      href="{{ asset('/favicon.svg') }}"
      rel="shortcut icon"
      media="(prefers-color-scheme: light)"
    >
    <link
      type="image/x-icon"
      href="{{ asset('/favicon-dark.svg') }}"
      rel="shortcut icon"
      media="(prefers-color-scheme: dark)"
    >
  @else
    <link
      type="image/png"
      href="/favicon-32x32.png"
      rel="icon"
      sizes="32x32"
    >
    <link
      type="image/png"
      href="/favicon-16x16.png"
      rel="icon"
      sizes="16x16"
    >
  @endif
  <link
    href="{{ asset('/apple-touch-icon.png') }}"
    rel="apple-touch-icon"
    sizes="180x180"
  >
  <link
    href="/site.webmanifest"
    rel="manifest"
  >

  <link
    type="image/png"
    href="{{ asset('/favicon-16x16.png') }}"
    rel="icon"
    sizes="16x16"
  >
  <meta
    name="msapplication-TileColor"
    content="{{ $tile }}"
  >
  <meta
    name="theme-color"
    content="{{ $theme }}"
  >

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

  @steward()

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

<body class="{{ config('app.env') === 'local' ? 'debug-screens' : '' }} font-sans antialiased">
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
