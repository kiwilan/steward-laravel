<div {{ $attributes->merge([
    'class' => 'hidden lg:block',
]) }}>
  <livewire:stw-listing.option.clear />
  <form id="filters">
    {{ $slot }}
  </form>
</div>
