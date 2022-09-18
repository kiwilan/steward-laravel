import { ChainedCommands } from '@tiptap/core';

declare type Mark = 'bold' | 'italic' | 'strike' | 'code' | 'highlight' | 'link' | 'subscript' | 'superscript' | 'textstyle' | 'underline';
declare type Node = 'blockquote' | 'bulletList' | 'codeBlock' | 'document' | 'emoji' | 'hardBreak' | 'hashtag' | 'heading' | 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6' | 'horizontalRule' | 'image' | 'mention' | 'orderedList' | 'paragraph' | 'table' | 'tableRow' | 'tableCell' | 'taskList' | 'taskItem' | 'text' | 'youTube';
declare type Extra = 'clearNodes' | 'undo' | 'redo' | 'separator';
declare type Command = Mark | Node | Extra;
interface ActionButton {
    /**
     * Name for action
     */
    title: string;
    /**
     * Shortcut key for the button
     * From: https://tiptap.dev/api/keyboard-shortcuts
     * And markdown shortcuts: https://tiptap.dev/examples/markdown-shortcuts
     * And typography: https://tiptap.dev/api/extensions/typography
     */
    hotkey?: string;
    /**
     * Optional SVG icon
     */
    icon?: string;
    /**
     * Command name
     */
    command: Command;
    /**
     * Extra parameters
     */
    params?: {
        level?: number;
    };
    isStarterKit?: boolean;
    isPro?: boolean;
    onlyTitle?: boolean;
    type?: 'mark' | 'node' | 'extra';
}

/**
 * Tiptap editor
 * @param {HTMLElement} editorReference
 *
 * Helped with: https://github.com/ueberdosis/tiptap/issues/1515#issuecomment-903095273
 */
declare const Tiptap: () => {
    content: string;
    actions: ActionButton[];
    updatedAt: number;
    $wire: {
        content: string;
    };
    init(): void;
    isActive(action: ActionButton): boolean;
    isChainedCommands(method: ChainedCommands): method is ChainedCommands;
    command(action: ActionButton): void;
    countCharacters(): any;
    countWords(): any;
};

export { Tiptap };
