@props(['align' => 'right', 'size' => 'lg', 'contentClasses' => 'py-1 bg-white dark:bg-gray-700', 'dropdownClasses' => ''])

@php
  switch ($align) {
      case 'left':
          $alignmentClasses = 'origin-top-left left-0';
          break;
      case 'top':
          $alignmentClasses = 'origin-top';
          break;
      case 'none':
      case 'false':
          $alignmentClasses = '';
          break;
      case 'right':
      default:
          $alignmentClasses = 'origin-top-right right-0';
          break;
      case 'bottom':
          $alignmentClasses = 'bottom-full';
          break;
  }
  
  $width = 'w-48';
  switch ($size) {
      case 'sm':
          $width = 'w-24';
          break;
      case 'md':
          $width = 'w-32';
          break;
      case 'lg':
          $width = 'w-48';
          break;
      case 'xl':
          $width = 'w-64';
          break;
  }
@endphp

<div
  class="relative"
  x-data="{ open: false }"
  @click.away="open = false"
  @close.stop="open = false"
>
  <div @click="open = ! open">
    {{ $trigger ?? 'Error' }}
  </div>

  <div
    class="{{ $width }} {{ $alignmentClasses }} {{ $dropdownClasses }} absolute z-50 mt-2 rounded-md shadow-lg"
    style="display: none;"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="transform opacity-0 scale-95"
    x-transition:enter-end="transform opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-75"
    x-transition:leave-start="transform opacity-100 scale-100"
    x-transition:leave-end="transform opacity-0 scale-95"
    @click="open = false"
  >
    <div class="{{ $contentClasses }} rounded-md ring-1 ring-black ring-opacity-5">
      {{ $content ?? 'Error' }}
    </div>
  </div>
</div>
