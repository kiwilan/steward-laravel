<div {{ $attributes->merge([
    'class' => 'hidden lg:block',
]) }}>
  <livewire:listing.option.clear />
  <form id="filters">
    {{ $slot }}
  </form>
</div>
