import type { ChainedCommands } from '@tiptap/core'
import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import Typography from '@tiptap/extension-typography'
import CharacterCount from '@tiptap/extension-character-count'
import Link from '@tiptap/extension-link'

interface EditorT extends Editor {}

export type { ChainedCommands, EditorT }
export default {
  Editor,
  StarterKit,
  Typography,
  CharacterCount,
  Link,
}
