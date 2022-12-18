<div>
  @push('head')
    <link
      href="https://cdn.quilljs.com/1.3.6/quill.snow.css"
      rel="stylesheet"
    >
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <style>
      .ql-toolbar {
        border-top-right-radius: 0.375rem;
        border-top-left-radius: 0.375rem;
      }

      .ql-toolbar button {
        border-radius: 0.375rem;
        transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
        background-color: transparent;
      }

      .ql-toolbar .ql-active {
        color: #000 !important;
        background-color: rgba(0, 0, 0, .1) !important;
      }

      .ql-container {
        border-bottom-right-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
      }

      .ql-editor {
        min-height: 5rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
      }
    </style>
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
                  placeholder: '',
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
      
              const strip_tags = (input, allowed) => {
                  allowed = (
                      ((allowed || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []
                  ).join('') // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
                  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
                      commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi
                  return input.replace(commentsAndPhpTags, '').replace(tags, function($0, $1) {
                      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
                  })
              }
      
              const name = '{{ $attributes->get('wire:model') }}'
              const content = htmlToDelta(this.content)
              quill.setContents(content)
      
              quill.on('text-change', (delta, oldDelta, source) => {
                  let html = quill.root.innerHTML
                  html = strip_tags(
                      html,
                      '<strong><em><u><p><li><ol><ul><blockquote><a><h2><h3><h4><h5><h6><br>'
                  );
                  @this.set(name, html)
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
