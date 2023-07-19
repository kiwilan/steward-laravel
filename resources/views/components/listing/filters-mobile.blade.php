<div
  x-data
  x-cloak
>
  <div
    class="relative z-40 lg:hidden"
    role="dialog"
    aria-modal="true"
    x-show="$store.slideOver.showLayer"
    x-transition
  >
    <div
      class="fixed inset-0 bg-black bg-opacity-25 transition-opacity duration-300 ease-linear"
      x-show="$store.slideOver.showLayer"
      :class="$store.slideOver.showOverlay ? 'opacity-100' : 'opacity-0'"
    ></div>

    <div
      class="fixed inset-0 z-40 flex"
      x-show="$store.slideOver.showLayer"
    >
      <div
        class="relative ml-auto flex h-full w-full max-w-xs transform flex-col overflow-y-auto bg-white py-4 pb-12 shadow-xl transition duration-300 ease-in-out"
        x-show="$store.slideOver.showLayer"
        :class="$store.slideOver.isOpen ? 'translate-x-0' : 'translate-x-full'"
        @click.outside="$store.slideOver.close()"
      >
        <div class="flex items-center justify-between px-4">
          <h2 class="text-lg font-medium text-gray-900">Filters</h2>
          <button
            class="-mr-2 flex h-10 w-10 items-center justify-center rounded-md bg-white p-2 text-gray-400"
            type="button"
            @click="$store.slideOver.close()"
          >
            <span class="sr-only">Close menu</span>
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <!-- Filters -->
        <form
          class="mt-4 border-t border-gray-200 px-3"
          id="filters-mobile"
          x-data="{
              init() {
                  let filtersMobile = document.getElementById('filters-mobile')
                  let inputs = filtersMobile.getElementsByTagName('input')
                  let labels = filtersMobile.getElementsByTagName('label')
          
                  for (item of inputs) {
                      let idAttr = item.getAttribute('id')
                      item.setAttribute('id', 'mobile-' + idAttr)
                  }
          
                  for (item of labels) {
                      let forAttr = item.getAttribute('for')
                      item.setAttribute('for', 'mobile-' + forAttr)
                  }
              }
          }"
          x-cloak
        >
          <livewire:listing.option.clear />
          {{ $slot }}
        </form>
      </div>
    </div>
  </div>
