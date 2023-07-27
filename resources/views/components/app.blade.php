<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <x-stw-head-meta
    :dark="$dark"
    :tile="$tile"
    :theme="$theme"
  />

  @isset($head)
    {{ $head }}
  @endisset

  @if ($dark)
    @darkMode
  @endif
  @stack('head')
</head>

<body class="{{ config('app.env') === 'local' ? 'debug-screens' : '' }} font-sans antialiased">
  {{ $slot }}

  @isset($scripts)
    {{ $scripts }}
  @endisset
  @stack('modals')
  @stack('scripts')
</body>

</html>
