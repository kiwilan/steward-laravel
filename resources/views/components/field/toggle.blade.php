<div
  x-data="toggle"
  class="flex items-center justify-between"
>
  <input
    x-ref="toggleEl"
    {{ $attributes->whereStartsWith('wire:model') }}
    type="checkbox"
    class="hidden"
  >
  <span
    class="flex flex-grow flex-col"
    @click="toggleValue()"
  >
    <span
      class="text-sm font-medium text-gray-900"
      id="availability-label"
    >
      {{ $label }}
    </span>
    <span
      class="text-sm text-gray-500"
      id="availability-description"
    >
      {{ $hint }}
    </span>
  </span>
  <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
  <button
    type="button"
    :class="toggled ? 'bg-indigo-600' : 'bg-gray-200'"
    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
    role="switch"
    aria-checked="false"
    aria-labelledby="availability-label"
    aria-describedby="availability-description"
    @click="toggleValue()"
  >
    <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
    <span
      aria-hidden="true"
      :class="toggled ? 'translate-x-5' : 'translate-x-0'"
      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
    ></span>
  </button>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('toggle', () => ({
      toggled: false,

      init() {
        this.toggled = this.$refs.toggleEl.checked
      },
      toggleValue() {
        this.toggled = !this.toggled
        this.$wire.set(this.$refs.toggleEl.getAttribute('wire:model'), this.toggled)
      },
    }))
  })
</script>
