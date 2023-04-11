import type { Alpine } from 'alpinejs'
import type { ChainedCommands, EditorT } from '../../tiptap.js'
import Tiptap from '../../tiptap.js'
import type { ActionButton } from './actions.js'
import { ExecuteCommand, Extras, Marks, Nodes } from './actions.js'

interface EditorActions {
  bold: boolean
  italic: boolean
  strike: boolean
  code: boolean
  link: boolean
  h1: boolean
  h2: boolean
  h3: boolean
  seperator: boolean
  codeBlock: boolean
  blockquote: boolean
  bulletList: boolean
  orderedList: boolean
  horizontalRule: boolean
  clear: boolean
  undo: boolean
  redo: boolean
}

let refs: {
  editorReference: HTMLElement
}

let editor: EditorT

/**
 * Tiptap editor
 *
 * Helped with: https://github.com/ueberdosis/tiptap/issues/1515#issuecomment-903095273
 */
export default (Alpine: Alpine) => {
  Alpine.data('editor', () => ({
    content: '<p>This is where the content goes</p>',
    actions: [] as ActionButton[],
    updatedAt: Date.now(),
    $wire: {
      content: '',
    },
    init(actions: EditorActions = {
      bold: true,
      italic: true,
      strike: true,
      code: false,
      link: true,
      h1: false,
      h2: true,
      h3: true,
      seperator: true,
      codeBlock: false,
      blockquote: true,
      bulletList: true,
      orderedList: false,
      horizontalRule: false,
      clear: true,
      undo: true,
      redo: true,
    }) {
      // eslint-disable-next-line @typescript-eslint/prefer-ts-expect-error
      // @ts-ignore - this is a reference to the Alpine data object
      refs = this.$refs

      editor = new Tiptap.Editor({
        element: refs.editorReference,
        extensions: [
          Tiptap.StarterKit,
          Tiptap.Typography,
          // CharacterCount.configure({
          //   limit: this.limit,
          // }),
          Tiptap.CharacterCount,
          Tiptap.Link,
        ],
        content: this.content,
        onCreate: () => {
          this.updatedAt = Date.now()
          this.content = editor.getHTML()
          this.$wire.content = this.content
        },
        onUpdate: ({ editor }) => {
          this.updatedAt = Date.now()
          this.content = editor.getHTML()
          this.$wire.content = this.content
        },
        onTransaction: () => {
          this.updatedAt = Date.now()
        },
      })

      const actionsAvailable = {
        bold: Marks.bold,
        italic: Marks.italic,
        strike: Marks.strike,
        code: Marks.code,
        link: Marks.link,
        h1: Nodes.h1,
        h2: Nodes.h2,
        h3: Nodes.h3,
        seperator: Extras.separator,
        codeBlock: Nodes.codeBlock,
        blockquote: Nodes.blockquote,
        bulletList: Nodes.bulletList,
        orderedList: Nodes.orderedList,
        horizontalRule: Nodes.horizontalRule,
        clear: Extras.clearNodes,
        undo: Extras.undo,
        redo: Extras.redo,
        default: 'None',
      }
      this.actions = [
        Marks.bold,
        Marks.italic,
        Marks.strike,
        Marks.code,
        Marks.link,
        Nodes.h1,
        Nodes.h2,
        Nodes.h3,
        Extras.separator,
        Nodes.codeBlock,
        Nodes.blockquote,
        Nodes.bulletList,
        Nodes.orderedList,
        Nodes.horizontalRule,
        Nodes.hardBreak,
        Extras.separator,
        Extras.clearNodes,
        Extras.redo,
        Extras.undo,
      ]
    },
    isActive(action: ActionButton) {
      return editor.isActive(action.command, action.params)
    },
    isChainedCommands(method: ChainedCommands): method is ChainedCommands {
      return (<ChainedCommands>method).run() !== undefined
    },
    command(action: ActionButton) {
      ExecuteCommand(editor, action)
    },
    countCharacters() {
      return editor.storage.characterCount.characters()
    },
    countWords() {
      return editor.storage.characterCount.words()
    },
  }))
}
