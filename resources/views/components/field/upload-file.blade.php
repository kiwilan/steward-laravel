<div {{ $attributes }}>
  <div
    x-data="uploadFile"
    x-init="boot('{{ $accept }}', '{{ $accepted }}', '{{ $size }}')"
  >
    <div class="max-w-lg sm:pt-5">
      <label
        for="{{ $name }}"
        class="block text-sm font-medium text-gray-700 dark:text-gray-300 sm:mt-px sm:pt-2"
      >
        {{ $label }}
      </label>
      <div
        x-show="files && files.length"
        x-transition
        class="flex justify-between text-sm"
      >
        <div class="flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400">
          <div
            x-text="accepted"
            class="uppercase"
          ></div>
          <span>up to</span>
          <span x-text="size"></span>
        </div>
        <button @click="clearFiles()">
          Clear all files
        </button>
      </div>
      <div class="mt-2 sm:col-span-2">
        <div
          class="relative flex cursor-pointer justify-center rounded-md border-2 border-dashed border-gray-300 px-6 pt-5 pb-6 transition-colors hover:bg-gray-50 hover:dark:bg-gray-800"
          x-on:click="$refs.fileInput.click()"
          x-on:drop="dropHandler(event)"
          x-on:dragover="dragOverHandler(event)"
        >
          <input
            x-ref="fileInput"
            name="{{ $name }}"
            type="file"
            multiple
            class="sr-only"
            :accept="accept"
            x-on:change="upload(event)"
          >
          <template x-if="files && files.length === 0">
            <div class="space-y-1 text-center">
              <svg
                class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-300"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 48 48"
                aria-hidden="true"
              >
                <path
                  d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              <div class="flex text-sm text-gray-600 dark:text-gray-400">
                <label
                  for="file-upload"
                  class="relative cursor-pointer rounded-md font-medium text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500 dark:text-indigo-400"
                >
                  <span>Upload a file</span>
                  <input
                    id="file-upload"
                    name="file-upload"
                    type="file"
                    class="sr-only"
                  >
                </label>
                <p class="pl-1">or drag and drop</p>
              </div>
              <div class="flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400">
                <div
                  x-text="accepted"
                  class="uppercase"
                ></div>
                <span>up to</span>
                <span x-text="size"></span>
              </div>
            </div>
          </template>
          <template x-if="files && files.length">
            <ul class="mt-2 grid grid-cols-3 gap-4">
              <template x-for="(file,id) in files">
                <li
                  :id="`file-upload-${id}`"
                  class="relative h-32"
                >
                  <div
                    :id="`file-preview-${id}`"
                    class="relative h-full"
                  >
                    <img
                      :src="preview(file, id)"
                      class="hidden h-32 w-full rounded-md object-cover"
                    >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="absolute top-1/2 left-1/2 hidden h-20 w-20 -translate-x-1/2 -translate-y-1/2"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                      />
                    </svg>
                    <div class="absolute inset-0 flex rounded-md bg-gray-800 bg-opacity-50 p-1">
                      <div class="mx-auto mt-auto w-full">
                        <div
                          x-text="extension(file)"
                          class="mx-auto w-max rounded-md bg-gray-800 bg-opacity-90 p-1 text-center text-xs uppercase"
                        ></div>
                        <div
                          x-text="file.name"
                          class="line-clamp-1 inline-block w-full overflow-hidden text-ellipsis break-words text-center text-xs"
                        ></div>
                      </div>
                    </div>
                  </div>
                  <button
                    class="absolute top-1 right-1 z-20 rounded-md bg-gray-700 p-0.5 transition-colors hover:bg-gray-800"
                    @click="remove(file, id)"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="h-5 w-5"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </button>
                </li>
              </template>
            </ul>
          </template>
        </div>
      </div>
    </div>
  </div>
</div>

<script lang="ts">
  document.addEventListener('alpine:init', () => {
    Alpine.data('uploadFile', () => ({
      files: [],
      file: undefined,
      media: undefined,
      preview: undefined,
      accept: 'image/jpeg,image/png,image/webp',
      accepted: '',
      size: '10 MB',

      boot(accept, accepted, size) {
        this.accept = accept
        this.size = size
        accept = accept.split(',')
        accept.forEach(el => {
          let type = el.split('/')[1]
          this.accepted += type + ', '
        })
        this.accepted = this.accepted.slice(0, -2)
      },
      clearFiles() {
        this.files = []
      },
      upload() {
        let files = event.target.files
        if (files) {
          Object.entries(files).forEach(file => {
            let [key, value] = file
            this.files.push(value)
          });
        }
      },
      dropHandler(ev) {
        ev.preventDefault();

        if (ev.dataTransfer.items) {
          // Use DataTransferItemList interface to access the file(s)
          [...ev.dataTransfer.items].forEach((item, i) => {
            // If dropped items aren't files, reject them
            if (item.kind === 'file') {
              const file = item.getAsFile();
              // console.log(`… file[${i}].name = ${file.name}`);
              this.files.push(file);
            }
          });
        } else {
          // Use DataTransfer interface to access the file(s)
          [...ev.dataTransfer.files].forEach((file, i) => {
            // console.log(`… file[${i}].name = ${file.name}`);
          });
        }
      },
      dragOverHandler(ev) {
        // Prevent default behavior (Prevent file from being opened)
        ev.preventDefault();
      },
      remove(file, id) {
        this.files.splice(id, 1)
      },
      extension(file) {
        return file.name.split('.').pop();
      },
      preview(file, id) {
        let preview = document.getElementById(`file-preview-${id}`)
        let svg = preview.getElementsByTagName('svg')[0]
        let img = preview.getElementsByTagName('img')[0]

        let isAccepted = file && this.accepted.includes(file['type'])
        let isImage = file && file['type'].split('/')[0] === 'image'
        if (isImage) {
          img.classList.remove('hidden')
          return URL.createObjectURL(file)
        } else {
          svg.classList.remove('hidden')
        }
      },
      humanFileSize(size) {
        const i = Math.floor(Math.log(size) / Math.log(1024));
        return (
          (size / Math.pow(1024, i)).toFixed(2) * 1 +
          " " + ["B", "kB", "MB", "GB", "TB"][i]
        );
      },
    }))
  })
</script>
