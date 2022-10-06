<div>
  <label
    for="country"
    class="block text-sm font-medium text-gray-700"
  >
    {{ $label }}
  </label>
  <select
    id="country"
    name="country"
    autocomplete="country-name"
    {{ $attributes->whereStartsWith('wire:model') }}
    class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
  >
    @foreach ($options as $key => $option)
      <option @if ($key === 0) disabled selected @endif>{{ $option }}</option>
    @endforeach
  </select>
</div>
