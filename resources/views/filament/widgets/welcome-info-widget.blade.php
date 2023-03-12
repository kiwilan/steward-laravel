<x-filament::widget class="filament-filament-info-widget">
  <x-filament::card class="relative">
    <div class="relative flex h-12 flex-col items-center justify-center space-y-2">
      <div class="space-y-1">
        <a
          href="/"
          @class([
              'flex items-end space-x-2 rtl:space-x-reverse text-gray-800 hover:text-primary-500 transition text-xl font-semibold',
              'dark:text-primary-500 dark:hover:text-primary-400' => config(
                  'filament.dark_mode'),
          ])
        >
          {{ config('app.name') }}
        </a>
      </div>

      <div class="flex space-x-2 text-sm rtl:space-x-reverse">
        <a
          href="{{ \Kiwilan\Steward\StewardConfig::filamentWidgetsWelcomeUrl() }}"
          target="_blank"
          rel="noopener noreferrer"
          @class([
              'text-gray-600 hover:text-primary-500 focus:outline-none focus:underline',
              'dark:text-gray-300 dark:hover:text-primary-500' => config(
                  'filament.dark_mode'),
          ])
        >
          {{ __(\Kiwilan\Steward\StewardConfig::filamentWidgetsWelcomeLabel()) }}
        </a>

        {{-- <span>
          &bull;
        </span>

        <a
          href="https://github.com/filamentphp/filament"
          target="_blank"
          rel="noopener noreferrer"
          @class([
              'text-gray-600 hover:text-primary-500 focus:outline-none focus:underline',
              'dark:text-gray-300 dark:hover:text-primary-500' => config(
                  'filament.dark_mode'
              ),
          ])
        >
          {{ __('filament::widgets/filament-info-widget.buttons.visit_github.label') }}
        </a> --}}
      </div>
    </div>

    <div class="absolute right-0 bottom-0 h-16 w-16 rtl:right-auto rtl:left-0">
      <img
        class="dark:hidden"
        src="{{ asset(\Kiwilan\Steward\StewardConfig::filamentLogoDefault()) }}"
        alt=""
      >
      <img
        class="hidden dark:block"
        src="{{ asset(\Kiwilan\Steward\StewardConfig::filamentLogoDark()) }}"
        alt=""
      >
    </div>
  </x-filament::card>
</x-filament::widget>
