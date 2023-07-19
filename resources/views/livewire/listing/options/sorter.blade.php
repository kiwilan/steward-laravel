<div
  class="flex items-center"
  x-cloak
  x-data="{
      expanded: false,
  
      init() {
          if (this.current?.length > 0) {
              this.$wire.set('expanded', 1)
          }
      },
  }"
>
  <x-dropdown :label="$label">
    @slot('trigger')
      <button
        class="relative flex items-center gap-2 rounded-md px-3 py-2 text-sm transition-colors hover:bg-gray-100"
        type="button"
      >
        {{ $label }}
        <div class="absolute -bottom-4 right-0 line-clamp-1 w-32 text-center text-xs">
          {{ $labelCurrent }}
        </div>
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
      <div class="m-2 space-y-2">
        @loop($options as $value => $name)
          <button
            class="flex w-full items-center px-2 py-1 text-left text-sm text-gray-600 transition-colors hover:bg-gray-50"
            type="button"
            wire:click="select('{{ $value }}')"
            @click="open = false"
          >
            {{ $name }}
          </button>
        @endloop
      </div>
    @endslot
  </x-dropdown>

  <div>
    <button
      class="rounded-md p-1 transition-colors hover:bg-gray-100"
      wire:click="reverseSort()"
    >
      <svg
        class="{{ $isDesc ? 'rotate-180' : '' }} h-6 w-6 text-gray-400 transition-transform"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          fillRule="evenodd"
          d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
          clipRule="evenodd"
        />
      </svg>
    </button>
  </div>
</div>
