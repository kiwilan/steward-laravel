@props(['title' => '', 'subtitle' => '', 'name' => '', 'sorters' => '', 'filters' => ''])

<div>
  @if ($filterable)
    <x-listing.filters-mobile>
      {{ $filters }}
    </x-listing.filters-mobile>
  @endif

  <main>
    <div class="border-b border-gray-200 pb-6 pt-12 sm:flex sm:items-end sm:justify-between">
      <div>
        <h1 class="text-4xl font-bold tracking-tight text-gray-900">
          {{ $title }}
        </h1>
        @isset($subtitle)
          <p>
            {{ $subtitle }}
          </p>
        @endisset
      </div>

      <x-listing.sorters class="mt-3 sm:mt-0">
        @if ($sortable)
          <livewire:listing.option.sorter
            name="sort"
            label="Sort"
            :options="$sortable"
            :current="$sort"
          />
        @endif
        {{ $sorters }}
      </x-listing.sorters>
    </div>

    <section
      class="pb-24 pt-6"
      aria-labelledby="{{ $name }}-heading"
    >
      <h2
        class="sr-only"
        id="{{ $name }}-heading"
      >
        {{ $title }}
      </h2>

      <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4">
        @if ($filterable)
          <x-listing.filters>
            {{ $filters }}
          </x-listing.filters>
        @endif
        <div class="{{ $filterable ? 'lg:col-span-3' : 'lg:col-span-4' }}">
          @if ($searchable)
            <x-listing.search class="pb-5" />
          @endif
          <div class="listing">
            {{ $slot }}
          </div>
        </div>
      </div>

      @if ($paginate)
        <div class="mt-16">
          <x-listing.pagination-size :options="$paginationSizeOptions" />
          {{ $paginate->onEachSide(2)->links() }}
        </div>
      @endif
    </section>
  </main>
</div>
