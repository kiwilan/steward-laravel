<button
  class="flex w-full items-center space-x-1 rounded-md px-2 py-2 text-sm transition-colors hover:bg-gray-100"
  wire:click="clear"
>
  <svg
    class="h-4 w-4"
    xmlns="http://www.w3.org/2000/svg"
    fill="none"
    viewBox="0 0 24 24"
    stroke="currentColor"
    stroke-width="2"
  >
    <path
      stroke-linecap="round"
      stroke-linejoin="round"
      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
    />
  </svg>
  <div>Clear all filters</div>
</button>
