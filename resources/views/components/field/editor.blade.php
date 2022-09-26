<style>
  [x-cloak] {
    display: none !important;
  }
</style>
<div class="tiptap">
  <div
    x-data="editor"
    x-cloak
    wire:ignore
    {{ $attributes->whereDoesntStartWith('wire:model') }}
    class="tiptap-wrapper tiptap-border tiptap-bg"
  >
    <div class="tiptap-actions tiptap-border tiptap-bg">
      <template
        x-for="(action,index) in actions"
        :key="index"
      >
        <div class="flex h-8 w-max items-center">
          <button
            x-show="action.command !== 'separator'"
            @click="command(action)"
            :class="{ 'tiptap-action_active': isActive(action) }"'"
            class="tiptap-action"
          >
            <span
              x-html="action.icon ? action.icon : action.title"
              :title="`${action.title} (${action.hotkey})`"
            ></span>
          </button>
          <div
            x-show="action.command === 'separator'"
            class="mx-1 flex h-full w-1"
          >
            <div class="m-auto h-3/5 w-[1px] bg-gray-400 dark:bg-gray-600">
            </div>
          </div>
        </div>
      </template>
    </div>

    <div
      x-ref="editorReference"
      class="tiptap-editor"
    >
    </div>
    <div class="tiptap-footer tiptap-border tiptap-bg">
      <div class="tiptap-footer_wrapper">
        <div class="tiptap-footer_characters flex items-center space-x-1">
          <div>
            <span x-text="countWords"></span>
            words
          </div>
          <div>
            (<span x-text="countCharacters"></span>
            characters)
          </div>
        </div>
        <div class="tiptap-footer_helper">
          <span>Powered by
            <a
              href="https://tiptap.dev"
              target="_blank"
              rel="noopener noreferrer"
            >Tiptap editor</a> with <a
              href="https://www.markdownguide.org/cheat-sheet"
              target="_blank"
              rel="noopener noreferrer"
            >Markdown
              syntax</a>.
          </span>
        </div>
      </div>
    </div>
  </div>

</div>
