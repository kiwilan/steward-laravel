@props(['options' => []])

<div class="relative">
  <x-stw-dropdown
    align="left"
    size="sm"
  >
    @slot('trigger')
      <button
        class="relative flex items-center gap-2 rounded-md px-3 py-2 text-sm transition-colors hover:bg-gray-100"
        type="button"
      >
        {{ __('listing.per-page') }}
        <!-- Heroicon: chevron-down -->
        <svg
          class="h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </button>
    @endslot
    @slot('content')
      <ul x-data="{
          pagination(size) {
              $wire.emit('paginationSize', size)
          }
      }">
        @loop($options as $option)
          <li>
            <button
              class="flex w-full items-center px-2 py-1 text-center text-sm text-gray-600 transition-colors hover:bg-gray-50"
              type="button"
              @click="pagination('{{ $option }}')"
            >
              {{ $option }}
            </button>
          </li>
        @endloop
      </ul>
    @endslot
  </x-stw-dropdown>
</div>
