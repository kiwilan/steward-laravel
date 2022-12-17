<div>
  @push('head')
    <link
      href="https://cdn.quilljs.com/1.3.6/quill.snow.css"
      rel="stylesheet"
    >
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  @endpush
  @php
    $bytes = random_bytes(20);
    $token = bin2hex($bytes);
  @endphp
  <div
    x-data="{
        content: @entangle($attributes->wire('model')),
        init() {
            const quill = new Quill('#{{ $token }}', {
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
  >
    <div
      id="{{ $token }}"
      {{ $attributes }}
    ></div>
  </div>
</div>
