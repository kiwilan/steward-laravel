<div class="tiptap group relative">
    <div class="flex">
        <button type="button"
            wire:click="togglePreview"
            class="relative z-20 ml-auto rounded-md py-1 px-2 hover:bg-gray-100 dark:hover:bg-gray-800">
            @if ($preview)
                Editor
            @else
                Preview
            @endif
        </button>
    </div>
    <div class="editor relative pt-1">
        <div @class([
            'transition-opacity relative z-10',
            'opacity-0' => $preview,
            'opacity-100' => !$preview,
        ])>
            <x-field.editor wire:model="content" />
        </div>
        <div @class([
            'transition-opacity absolute inset-0 border tiptap-border rounded-md p-2 mt-1 tiptap-bg prose dark:prose-invert max-w-full',
            'opacity-100' => $preview,
            'opacity-0' => !$preview,
        ])>
            {!! $content !!}
        </div>
    </div>
</div>
