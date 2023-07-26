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

@if ($dark)
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

@if ($dark)
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
