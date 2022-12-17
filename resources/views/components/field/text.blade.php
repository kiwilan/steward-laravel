@props(['helper', 'hint', 'prefix'])

@php
  $has_prefix = false;
  if (isset($prefix)) {
      $has_prefix = true;
  }
  
  $has_hint = false;
  if (isset($hint)) {
      $has_hint = true;
  }
@endphp

<div {{ $attributes }}>
  <div class="flex items-center justify-between text-gray-500">
    <label
      for="{{ $name }}"
      class="block text-sm font-medium text-gray-700"
    >
      {{ $label }}
      @if ($required)
        <span class="text-sm text-red-600">*</span>
      @endif
    </label>
    <div class="text-xs italic">
      {{ $hint }}
    </div>
    @if ($pattern)
      <div
        x-data="{ show: false }"
        class="relative"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="h-6 w-6 cursor-pointer"
          @mouseenter="show = true"
          @mouseleave="show = false"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"
          />
        </svg>
        <div
          x-show="show"
          x-transition
          class="absolute right-0 z-10 w-64 rounded-md bg-gray-100 p-2 text-xs shadow"
        >
          {{ $pattern }}
        </div>
      </div>
    @endif
  </div>
  <div
    x-data="{
        show: false,
    }"
    class="relative mt-1"
  >
    @if ($multiline)
      <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        cols="30"
        rows="5"
        required="{{ $required }}"
        {{ $attributes->whereStartsWith('wire:model') }}
        class="focus:border-primary-500 focus:ring-primary-500 mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"
        @isset($min) minlength="{{ $min }}" @endisset
        @isset($max) maxlength="{{ $max }}" @endisset
      ></textarea>
    @else
      <div class="mt-1 flex rounded-md shadow-sm">
        @if ($has_prefix)
          <span
            class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500"
          >
            <span>{{ $prefix }}</span>
          </span>
        @endif
        <input
          {{ $attributes->whereStartsWith('wire:model') }}
          id="{{ $name }}"
          name="{{ $name }}"
          @if ($type === 'password') :type="show ? 'text' : 'password'" @else type="{{ $type }}" @endif
          autocomplete="{{ $name }}"
          @if ($required) required="{{ $required }}" @endif
          placeholder="{{ $placeholder }}"
          {{ $readonly ? 'readonly' : '' }}
          @class([
              'block w-full appearance-none border border-gray-300 px-3 py-2 placeholder-gray-400 focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm',
              'rounded-md' => !$has_prefix,
              'rounded-r-md' => $has_prefix,
              'bg-gray-100' => $readonly,
          ])
          @isset($regex)
            onkeydown="return /{{ $regex }}/.test(event.key)"
          @endisset
          @isset($min)
            minlength="{{ $min }}"
          @endisset
          @isset($max)
            maxlength="{{ $max }}"
          @endisset
        >
        @if ($type === 'password')
          <button
            type="button"
            class="absolute top-1/2 right-2 -translate-y-1/2 rounded-md p-1"
            @click="show = !show"
          >
            <svg
              x-show="!show"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
              />
            </svg>
            <svg
              x-show="show"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
              />
            </svg>
          </button>
        @endif
      </div>
    @endif
  </div>
  <div class="mt-2 text-sm text-gray-500">
    {{ $helper }}
  </div>
  @isset($errors)
    @error($name)
      <span class="text-sm italic text-red-600">{{ $message }}</span>
    @enderror
  @endisset
</div>
