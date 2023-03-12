{{ \Filament\Facades\Filament::renderHook('footer.before') }}

<div class="filament-footer flex items-center justify-center">
  {{ \Filament\Facades\Filament::renderHook('footer.start') }}

  @if (config('filament.layout.footer.should_show_logo'))
    <a
      class="hover:text-primary-500 text-gray-300 transition"
      href="https://filamentphp.com"
      target="_blank"
      rel="noopener noreferrer"
    >
      <img
        class="dark:hidden"
        src="{{ asset(\Kiwilan\Steward\StewardConfig::filamentLogoInlineDefault()) }}"
        alt=""
      >
      <img
        class="hidden h-10 dark:block"
        src="{{ asset(\Kiwilan\Steward\StewardConfig::filamentLogoInlineDark()) }}"
        alt=""
      >
    </a>
  @endif

  {{ \Filament\Facades\Filament::renderHook('footer.end') }}
</div>

{{ \Filament\Facades\Filament::renderHook('footer.after') }}
