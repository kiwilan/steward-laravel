@if ($title)
  <meta
    property="og:title"
    content="{{ $title }}"
  >
  <meta
    name="twitter:title"
    content="{{ $title }}"
  >
@endif

@if ($description)
  <meta
    name="description"
    content="{{ $description }}"
  >
  <meta
    property="og:description"
    content="{{ $description }}"
  >
  <meta
    name="twitter:description"
    content="{{ $description }}"
  >
@endif

@if ($author)
  <meta
    name="author"
    content="{{ $author }}"
  />
  <meta
    name="twitter:creator"
    content="{{ $author }}"
  />
@endif

@if ($url)
  <meta
    property="og:url"
    content="{{ $url }}"
  >
  <meta
    property="twitter:url"
    content="{{ $url }}"
  >
  <meta
    property="twitter:domain"
    content="{{ $domain }}"
  >
@endif

@if ($image)
  <meta
    property="og:image"
    content="{{ $image }}"
  >
  <meta
    name="twitter:image"
    content="{{ $image }}"
  >
@endif

<meta
  property="og:type"
  content="website"
>
<meta
  name="twitter:card"
  content="{{ $twitter }}"
>
