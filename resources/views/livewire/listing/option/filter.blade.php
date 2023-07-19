<div
  x-cloak
  x-data="{
      expanded: @entangle('expanded'),
      current: @entangle('current'),
  
      init() {
          if (this.current?.length > 0) {
              this.$wire.set('expanded', 1)
          }
      },
  }"
>
  <div @class([
      'border-b py-2',
      'border-gray-200' => $border,
      'border-transparent' => !$border,
  ])>
    <div class="-my-3 flow-root">
      <button
        class="flex w-full items-center justify-between py-3 text-sm text-gray-400 hover:text-gray-500"
        type="button"
        aria-controls="filter-section-0"
        aria-expanded="false"
        @click="expanded = ! expanded"
      >
        <h3 class="font-medium text-gray-900">
          {{ $label }}
        </h3>
        <span class="ml-6 flex items-center">
          <svg
            class="h-5 w-5"
            aria-hidden="true"
            x-show="! expanded"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"
            />
          </svg>
          <svg
            class="h-5 w-5"
            aria-hidden="true"
            x-show="expanded"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H4.75A.75.75 0 014 10z"
              clip-rule="evenodd"
            />
          </svg>
        </span>
      </button>
    </div>

    <div
      class="pt-6"
      id="filter-section-0"
      x-show="expanded"
      x-collapse
    >
      <div class="max-h-full space-y-4 overflow-auto">
        @loop($options as $value => $name)
          <div class="flex items-center px-1 py-0.5">
            <input
              class="{{ $unique ? 'rounded-full' : 'rounded' }} text-primary-600 focus:ring-primary-500 h-4 w-4 border-gray-300"
              id="{{ $query }}-{{ $value }}"
              type="{{ $unique ? 'radio' : 'checkbox' }}"
              value="{{ $value }}"
              wire:model="current"
            >
            <label
              class="ml-3 text-sm text-gray-600"
              for="{{ $query }}-{{ $value }}"
            >
              {{ $name }}
            </label>
          </div>
        @endloop
      </div>
    </div>
  </div>
</div>
