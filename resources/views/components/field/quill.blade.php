<div>
  @push('head')
    <link
      href="https://cdn.quilljs.com/1.3.6/quill.snow.css"
      rel="stylesheet"
    >
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  @endpush
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
    @isset($_instance)
      <div
        x-data="{
            content: @entangle($attributes->wire('model')),
            init() {
                const quill = new Quill('#{{ $id }}', {
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            ['blockquote'],
                            [{ 'header': 2 }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            [
                                'link',
                                // 'image',
                                // 'video'
                            ],
                            ['clean']
                        ]
                    },
                    placeholder: 'Compose an epic...',
                    theme: 'snow'
                });
                const htmlToDelta = (html) => {
                    const div = document.createElement('div');
                    div.setAttribute('id', 'htmlToDelta');
                    div.innerHTML = `<div id='quillEditor' style='display:none'>${html}</div>`;
                    document.body.appendChild(div);
                    const quill = new Quill('#quillEditor', {
                        theme: 'snow',
                    });
                    const delta = quill.getContents();
                    document.getElementById('htmlToDelta').remove();
                    return delta;
                }
        
                const name = '{{ $attributes->get('wire:model') }}'
                const content = htmlToDelta(this.content)
                quill.setContents(content)
        
                quill.on('text-change', (delta, oldDelta, source) => {
                    @this.set(name, quill.root.innerHTML)
                });
            }
        }"
        wire:ignore
        class="relative mt-1"
      >
        <div
          id="{{ $id }}"
          {{ $attributes }}
        ></div>
      </div>
    @endisset
    <div class="mt-2 text-sm text-gray-500">
      {{ $helper }}
    </div>
    @isset($errors)
      @error($name)
        <span class="text-sm italic text-red-600">{{ $message }}</span>
      @enderror
    @endisset
  </div>
</div>
