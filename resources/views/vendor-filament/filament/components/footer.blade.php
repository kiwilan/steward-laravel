{{ \Filament\Facades\Filament::renderHook('footer.before') }}

<div class="filament-footer flex items-center justify-center">
  {{ \Filament\Facades\Filament::renderHook('footer.start') }}

  @if (config('filament.layout.footer.should_show_logo'))
    <a
      href="https://filamentphp.com"
      target="_blank"
      rel="noopener noreferrer"
      class="hover:text-primary-500 text-gray-300 transition"
    >
      <img
        src="{{ asset(config('steward.filament.logo-inline.default')) }}"
        alt=""
        class="dark:hidden"
      >
      <img
        src="{{ asset(config('steward.filament.logo-inline.dark')) }}"
        alt=""
        class="hidden h-10 dark:block"
      >
    </a>
  @endif

  {{ \Filament\Facades\Filament::renderHook('footer.end') }}
</div>

{{ \Filament\Facades\Filament::renderHook('footer.after') }}
