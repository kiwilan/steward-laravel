<x-dynamic-component
  :component="$getFieldWrapperView()"
  :field="$field"
>
  <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }">
    <a
      class="fi-btn fi-btn-size-md fi-btn-color-primary fi-ac-btn-action bg-custom-600 hover:bg-custom-500 focus:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus:ring-custom-400/50 relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-white shadow-sm outline-none transition duration-75 focus:ring-2 disabled:pointer-events-none disabled:opacity-70"
      href="{{ $field->getDownload() }}"
      style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
      download
    >
      <span class="fi-btn-label">
        {{ $field->getLabel() }}
      </span>
    </a>
  </div>
</x-dynamic-component>
