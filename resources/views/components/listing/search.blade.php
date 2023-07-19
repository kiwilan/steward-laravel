<div {{ $attributes }}>
  <div class="relative">
    <svg
      class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke-width="1.5"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
      />
    </svg>
    <input
      class="focus:border-primary-500 focus:ring-primary-500 block w-full rounded-md border border-gray-300 py-2 pl-9 pr-3 placeholder-gray-400 shadow-sm focus:outline-none sm:text-sm"
      type="search"
      placeholder="Chercher une figurine..."
      wire:model="q"
    >
  </div>
</div>
